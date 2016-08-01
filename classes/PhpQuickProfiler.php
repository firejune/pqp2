<?php

/*

 Title : PHP Quick Profiler Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This class processes the logs and organizes the data
 for output to the browser. Initialize this class with a start time at
 the beginning of your code, and then call the display method when your code
 is terminating.

*/

class PhpQuickProfiler {

	public static $path = '/pqp2/';
	public static $startTime = 0;
	public $globals = array();
	public $output = array();
	public $db = '';

	function __construct($home = '', $hook = true) {
		require_once($_SERVER['DOCUMENT_ROOT'].self::$path.'classes/Console.php');

		// Set strip path
		$this->home = console::$home = $home;

		// Dump keys of global valiables for diff
		foreach ($GLOBALS as $key => $val) {
			$this->globals[] = $key.'';
		}

    if ($hook) {
  		ini_set('display_errors', 0);
  		error_reporting(E_ALL);

  		set_error_handler('PhpQuickProfiler::handleError');
    }
	}

	// CONNECT TO DATABASE
	public function connectDatabase($conn, $db) {
  	require_once($_SERVER['DOCUMENT_ROOT'].self::$path.'classes/MySqlDatabase.php');
    $this->db = new MySqlDatabase($conn, $db);
	}

	// CUSTOM HANDLED ERROR MESSAGE
	public static function handleError($code, $message, $file = null, $line = null, $context = null) {
		if (error_reporting() === 0) {
			// This error code is not included in error_reporting
			return false;
		}

		$error = $log = null;
		switch ($code) {
			case E_PARSE:
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$error = 'Fatal Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_COMPILE_WARNING:
			case E_RECOVERABLE_ERROR:
				$error = 'Warning';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_STRICT:
				$error = 'Strict';
				break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$error = 'Deprecated';
				break;
			default :
  			$error = 'Unknown';
				break;
		}

		$logItem = array(
			'type' => 'error',
			'message' => '<em>'.$error.'</em>: '.$message,
			'file' => $file,
			'line' => $line,
			'context' => $context
		);

		console::error($logItem);

		return true;
	}

	// FORMAT THE DIFFERENT TYPES OF LOGS
	public function gatherConsoleData() {
		$logs = console::getLogs();

		if (count($logs['console'])) {
			foreach ($logs['console'] as $key => $log) {
				if ($log['type'] == 'log') {
					$logs['console'][$key]['message'] = print_r($log['message'], true);
					$logs['console'][$key]['time'] = $this->getReadableTime(($log['time'] - self::$startTime)*1000);
					$logs['console'][$key]['memory'] = $this->getReadableFileSize($log['memory']);
				}
				elseif ($log['type'] == 'error') {
					if (isset($logs['errorDuplicate'][$key])) {
						$logs['console'][$key]['duplicate'] = $logs['errorDuplicate'][$key];
					}
				}
				elseif ($log['type'] == 'memory') {
					$logs['console'][$key]['memory'] = $this->getReadableFileSize($log['memory']);
				}
				elseif ($log['type'] == 'speed') {
					$logs['console'][$key]['time'] = $this->getReadableTime(($log['time'] - self::$startTime)*1000);
				}
			}
		}

		$logs['console'] = array_reverse($logs['console']);
		$this->output['logs'] = $logs;
	}

	// AGGREGATE DATA ON THE FILES INCLUDED
	public function gatherFileData() {
		$files = get_included_files();
		$fileList = array();
		$fileTotals = array(
			'count' => count($files),
			'size' => 0,
			'largest' => 0,
		);

		$files = array_reverse($files);
		foreach ($files as $key => $file) {
			$size = filesize($file);
			$fileList[] = array(
					'path' => str_replace($this->home, '', $file),
					'size' => $this->getReadableFileSize($size)
				);
			$fileTotals['size'] += $size;
			if ($size > $fileTotals['largest']) $fileTotals['largest'] = $size;
		}

		$fileTotals['size'] = $this->getReadableFileSize($fileTotals['size']);
		$fileTotals['largest'] = $this->getReadableFileSize($fileTotals['largest']);
		$this->output['files'] = $fileList;
		$this->output['fileTotals'] = $fileTotals;
	}

	// MEMORY USAGE AND MEMORY AVAILABLE
	public function gatherMemoryData() {
		$memoryTotals = array();
		$memoryTotals['used'] = $this->getReadableFileSize(memory_get_peak_usage());
		$memoryTotals['total'] = ini_get('memory_limit');
		$this->output['memoryTotals'] = $memoryTotals;
	}

	// QUERY DATA -- DATABASE OBJECT WITH LOGGING REQUIRED
	public function gatherQueryData() {
		$duplicates = array();
		$queryTotals = array();
		$queryTotals['count'] = 0;
		$queryTotals['time'] = 0;
		$queryTotals['duplicate'] = 0;

		$queries = array();

		if ($this->db != '') {
			$queryTotals['count'] += $this->db->queryCount;
			foreach ($this->db->queries as $key => $query) {
				$query = $this->attemptToExplainQuery($query);
				$queryTotals['time'] += $query['time'];
				$query['time'] = $this->getReadableTime($query['time']);

				$sql = preg_replace('/\s+/', ' ', strtolower($query['sql']));
				if (isset($duplicates[$sql])) {
					$query['duplicate'] = $duplicates[$sql];
					$queryTotals['duplicate']++;
				}
				$queries[] = $query;
				$duplicates[$sql] = $this->db->queries;
			}
		}

		$queryTotals['time'] = $this->getReadableTime($queryTotals['time']);
		$this->output['queries'] = array_reverse($queries);
		$this->output['queryTotals'] = $queryTotals;
	}

	// CALL SQL EXPLAIN ON THE QUERY TO FIND MORE INFO
	function attemptToExplainQuery($query) {
		try {
			$sql = 'EXPLAIN '.$query['sql'];
			$rs = $this->db->query($sql);
		}
		catch(Exception $e) {}
		if ($rs) {
			$row = mysql_fetch_array($rs, MYSQL_ASSOC);
			$query['explain'] = $row;
		}
		return $query;
	}

	// SPEED DATA FOR ENTIRE PAGE LOAD
	public function gatherSpeedData() {
		$speedTotals = array();
		$speedTotals['total'] = $this->getReadableTime(($this->getMicroTime() - self::$startTime)*1000);
		$speedTotals['allowed'] = ini_get('max_execution_time');
		$this->output['speedTotals'] = $speedTotals;
	}

	// HELPER FUNCTIONS TO FORMAT DATA
	public static function getMicroTime() {
		$time = microtime();
		$time = explode(' ', $time);
		return $time[1] + $time[0];
	}

	public function getReadableFileSize($size, $retstring = null) {
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		if ($retstring === null) { $retstring = '%01.2f %s'; }

		$lastsizestring = end($sizes);

		foreach ($sizes as $sizestring) {
			if ($size < 1024) { break; }
			if ($sizestring != $lastsizestring) { $size /= 1024; }
		}
		if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
		return sprintf($retstring, $size, $sizestring);
	}

	public function getReadableTime($time) {
		$ret = $time;
		$formatter = 0;
		$formats = array('ms', 's', 'm');
		if ($time >= 1000 && $time < 60000) {
			$formatter = 1;
			$ret = ($time / 1000);
		}
		if ($time >= 60000) {
			$formatter = 2;
			$ret = ($time / 1000) / 60;
		}
		$ret = number_format($ret,3,'.','') . ' ' . $formats[$formatter];
		return $ret;
	}

	// DISPLAY TO THE SCREEN -- CALL WHEN CODE TERMINATING
	public function display($db = '', $master_db = '') {
		$this->db = $db;
		$this->master_db = $master_db;
		$this->gatherConsoleData();
		$this->gatherFileData();
		$this->gatherMemoryData();
		$this->gatherQueryData();
		$this->gatherSpeedData();

		require_once($_SERVER['DOCUMENT_ROOT'].self::$path.'display.php');

		ob_start();
		displayPqp($this->output, self::$path);
		return ob_get_contents();
	}

}

?>