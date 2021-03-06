<?php

/* - - - - - - - - - - - - - - - - - - - - - - - - - - -

 Title : Sample Landing page for PHP Quick Profiler Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This file contains the basic class shell needed
 to use PQP. In addition, the init() function calls for example
 usages of how PQP can aid debugging. See README file for help
 setting this example up.

- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

require_once('index.php');

class PQP2Example {

	private $profiler;
	private $db = '';

	public function __construct() {
		$this->profiler = new PhpQuickProfiler(dirname(__FILE__), false);
		/*
		$this->smarty = new Smarty();
		$this->smarty->template_dir = dirname(__FILE__).'/';
		$this->smarty->compile_dir  = SMARTY_DIR.'/templates_c/';
		$this->smarty->config_dir   = SMARTY_DIR.'/configs/';
		$this->smarty->cache_dir    = SMARTY_DIR.'/cache/';
		*/
	}

	public function init() {
		$this->sampleConsoleData();
		$this->sampleDatabaseData();
		$this->sampleMemoryLeak();
		$this->sampleSpeedComparison();
	}

	// EXAMPLES OF THE 4 CONSOLE FUNCTIONS
	public function sampleConsoleData() {
		try {
			console::log('Begin logging data');
			console::memory($this, 'PQP Example Class : Line '.__LINE__);
			console::speed('Time taken to get to line '.__LINE__);
			console::log(array('Name' => 'Ryan', 'Last' => 'Campbell'));
			console::speed('Time taken to get to line '.__LINE__);
			console::memory($this, 'PQP Example Class : Line '.__LINE__);
			console::log('Ending log below with a sample error.');
			throw new Exception('Unable to write to log!');
		}
		catch(Exception $e) {
			console::error($e, 'Sample error logging.');
		}
	}

	// DATABASE OBJECT TO LOG QUERIES
	public function sampleDatabaseData() {
		/*
		$db = 'your db name';
		$conn = mysql_connect(
			'your DB host',
			'your DB user',
			'your DB password'
		);
		mysql_select_db($db, $conn);

		$this->db = $this->profiler->connectDatabase($conn, $db);

		$sql = 'SELECT PostId FROM Posts WHERE PostId > 2';
		$rs = $this->db->query($sql);

		$sql = 'SELECT COUNT(PostId) FROM Posts';
		$rs = $this->db->query($sql);

		$sql = 'SELECT COUNT(PostId) FROM Posts WHERE PostId != 1';
		$rs = $this->db->query($sql);
		*/
	}

	// EXAMPLE MEMORY LEAK DETECTED
	public function sampleMemoryLeak() {
		$ret = '';
		$longString = 'This is a really long string that when appended with the . symbol
						will cause memory to be duplicated in order to create the new string.';
		for ($i = 0; $i < 10; $i++) {
			$ret = $ret . $longString;
			console::memory($ret, 'Watch memory leak -- iteration '.$i);
		}
	}

	// POINT IN TIME SPEED MARKS
	public function sampleSpeedComparison() {
		console::speed('Time taken to get to line '.__LINE__);
		console::speed('Time taken to get to line '.__LINE__);
		console::speed('Time taken to get to line '.__LINE__);
		console::speed('Time taken to get to line '.__LINE__);
		console::speed('Time taken to get to line '.__LINE__);
		console::speed('Time taken to get to line '.__LINE__);
	}

	public function __destruct() {
		$this->profiler->display();
		/*
		$this->smarty->assign('path', $this->profiler->path);
		$this->smarty->assign('output', $this->profiler->display(true));
		$this->smarty->display('pqp.tpl');
		*/
	}

}

$pqp = new PQP2Example();
$pqp->init();

?>
<!DOCTYPE html>
<html>
<head>
<title>PHP Quick Profiler #2 Demo</title>
<style type="text/css">
body{
	font-family:"Lucida Grande", Tahoma, Arial, sans-serif;
	margin:100px 0 0 0;
	background:#eee;
}
a {
	color: #7EA411;
}
h3 {
	line-height:160%;
}
#box {
	margin:100px auto 0 auto;
	width: 450px;
	padding:10px 20px 30px 20px;
	background-color: #FFF;
	border: 10px solid #dedede;
}
#box ul {
	margin:0 0 20px 0;
	padding:0;
}
#box li {
	margin:0 0 0 20px;
	padding:0 0 10px 0;
}
li a {
	color:blue;
}
strong a {
	color:#7EA411;
}
</style>

<body>
<div id="box">
	<h3>On this Page You Can See How to <br /> Use the PHP Quick Profiler #2 to...</h3>
	<ul>
	<li>Log PHP Objects. [ <a href="#" onclick="pQp.changeTab('console'); return false;">Demo</a> ]</li>
	<li>Watch as a string eats up memory. [ <a href="#" onclick="pQp.changeTab('memory'); return false;">Demo</a> ]</li>
	<li>Monitor our queries and their indexes. [ <a href="#" onclick="pQp.changeTab('queries'); return false;">Demo</a> ]</li>
	<li>Ensure page execution time is acceptable. [ <a href="#" onclick="pQp.changeTab('speed'); return false;">Demo</a> ]</li>
	<li>Prevent files from getting out of control. [ <a href="#" onclick="pQp.changeTab('files'); return false;">Demo</a> ]</li>
	</ul>

	<strong>Return to <a href="//github.com/firejune/pqp2/">GitHub</a>.</strong>
</div>
</body>
</html>