<?php

/*

 Title : PHP Quick Profiler MySQL Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : A simple database wrapper that includes
 logging of queries.

*/

class MySqlDatabase {

	private $conn;
	private $database;
	public $queryCount = 0;
	public $queries = array();

	// CONFIG CONNECTION
	function __construct($conn, $database) {
		if(!$conn) {
			throw new Exception('We\'re working on a few connection issues.');
		}
		$this->conn = $conn;
		$this->database = $database;
	}

	// QUERY
	function query($sql) {
		$start = $this->getTime();
		$rs = mysql_query($sql); // , $this->conn
		$this->queryCount += 1;
		$this->log($this->queryCount, $sql, $start);
		if (!$rs) {
			Console::error(new Exception('Could not execute query.'), 'Could not execute query.');
		} else {
			Console::log('QUERY SPEED POINT #'.$this->queryCount, $sql);
		}
		return $rs;
	}

	// DEBUGGING
	function log($id, $sql, $start) {
		$query = array(
			'id' => $id,
			'sql' => $sql,
			'time' => ($this->getTime() - $start)*1000
		);
		$this->queries[] = $query;
	}

	function getTime() {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$start = $time;
		return $start;
	}

	function __destruct() {
		@mysql_close($this->conn);
	}

}

?>
