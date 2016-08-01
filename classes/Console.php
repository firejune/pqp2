<?php

/*

 Title : PHP Quick Profiler Console Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This class serves as a wrapper around a global
 php variable, debugger_logs, that we have created.

*/

class Console {

	public static $home = '';
	private static $logs = array(
		'console' => array(),
		'memoryCount' => 0,
		'logCount' => 0,
		'speedCount' => 0,
		'errorCount' => 0,
		'errorDuplicate' => array()
	);

	// GET TRACE CONTEXT
	private static function getTrace($popCount = 1, $trace = null) {
		if (!$trace) $trace = debug_backtrace();

		$popCount++;
		for ($i = 0; $i < $popCount; $i++){
			array_shift($trace); // remove call to parent methods
		}

		$length = count($trace);
		$result = array();

		foreach ($trace as $key => $bt) {
			$args = '';
			foreach ($bt['args'] as $a) {
				if (!empty($args)) {
					$args .= ', ';
				}
				switch (gettype($a)) {
				case 'integer':
				case 'double':
					$args .= $a;
					break;
				case 'string':
					$a = htmlspecialchars(substr($a, 0, 32)).((strlen($a) > 32) ? '...' : '');
					$args .= "\"<b>$a</b>\"";
					break;
				case 'array':
					$args .= 'Array('.count($a).')';
					break;
				case 'object':
					$args .= 'Object('.get_class($a).')';
					break;
				case 'resource':
					$args .= 'Resource('.strstr($a, '#').')';
					break;
				case 'boolean':
					$args .= $a ? 'True' : 'False';
					break;
				case 'NULL':
					$args .= 'Null';
					break;
				default:
					$args .= 'Unknown';
				}
			}

			$path = str_replace(self::$home, '', $bt['file']);
			$result[] = ($length - $key).') <span class="red">'.$path.'</span>(<b>'.$bt['line'].'</b>): <span>'.$bt['function'].'</span>('.$args.')<br>';
		}

		return implode('', $result);
	}

	// LOG A VARIABLE TO CONSOLE
	public static function log($message, $query = '') {
		$e = error_get_last();
		$isQuery = strpos($message, 'QUERY') !== false;
		$logItem = array(
			'type' => 'log',
			'message' => $message,
			'query' => $query,
			'time' => PhpQuickProfiler::getMicroTime(),
			'memory' => memory_get_usage(),
			'context' => self::getTrace(2)
		);

		self::$logs['console'][] = $logItem;
		self::$logs['logCount'] += 1;
		self::$logs['speedCount'] += 1;
		self::$logs['memoryCount'] += 1;
	}

	// LOG MEMORY USAGE OF VARIABLE OR ENTIRE SCRIPT
	public static function memory($object, $message = 'Point in Usage', $min = 0) {
		$memory = strlen(serialize($object));
		if ($min < $memory) {
			$logItem = array(
				'type' => 'memory',
				'memory' => $memory,
				'message' => $message,
				'dataType' => gettype($object)
			);
			self::$logs['console'][] = $logItem;
			self::$logs['memoryCount'] += 1;
		}
	}

	// LOG A PHP EXCEPTION OBJECT
	public static function error($e, $m = '') {
		$trace = '';
		if (is_array($e) && isset($e['trace'])) {
			$trace = $e['trace'];
		} elseif ($e instanceof Exception) {
			$trace = $e->getTrace();
		}

		$logItem = array(
			'type' => 'error',
			'message' => $m ? $m : $e instanceof Exception ? $e->getMessage() : $e['message'],
			'file' => str_replace(self::$home, '', $e instanceof Exception ? $e->getFile() : $e['file']),
			'line' => $e instanceof Exception ? $e->getLine() : $e['line'],
			'context' => $trace
		);

		$idx = array_search($logItem, self::$logs['console']);
		if ($idx === false) {
			self::$logs['console'][] = $logItem;
		} else {
			if (!isset(self::$logs['errorDuplicate'][$idx])) {
				self::$logs['errorDuplicate'][$idx] = 1;
			}
			self::$logs['errorDuplicate'][$idx]++;
		}

		self::$logs['errorCount'] += 1;
	}

	// POINT IN TIME SPEED SNAPSHOT
	public static function speed($message = 'Point in Time') {
		$isQuery = strpos($message, 'QUERY') !== false;
		$logItem = array(
			'type' => 'speed',
			'time' => PhpQuickProfiler::getMicroTime(),
			'message' => $message
		);
		self::$logs['console'][] = $logItem;
		self::$logs['speedCount'] += 1;
	}

	// SET DEFAULTS & RETURN LOGS
	public static function getLogs() {
		return self::$logs;
	}
}

?>