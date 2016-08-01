<?php

/*

 Title : HTML Output for Php Quick Profiler
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This is a horribly ugly function used to output
 the PQP HTML. This is great because it will just work in your project,
 but it is hard to maintain and read. See the README file for how to use
 the Smarty file we provided with PQP.

*/

function displayPqp($output, $config) {
$cssUrl = $config.'css/pQp.css';

echo '<div id="pqp-container" class="pQp" style="display:none">';

$logCount = count($output['logs']['console']);
$fileCount = count($output['files']);
$memoryUsed = $output['memoryTotals']['used'];
$queryCount = $output['queryTotals']['count'];
$speedTotal = $output['speedTotals']['total'];

echo <<<PQPTABS
<div id="pQp" class="console" style="display: none">
<table id="pqp-metrics" cellspacing="0">
<tr>
	<td class="green" id="tab-console">
		<var>$logCount</var>
		<h4>Console</h4>
	</td>
	<td class="blue" id="tab-speed">
		<var>$speedTotal</var>
		<h4>Load Time</h4>
	</td>
	<td class="purple" id="tab-queries">
		<var>$queryCount Queries</var>
		<h4>Database</h4>
	</td>
	<td class="orange" id="tab-memory">
		<var>$memoryUsed</var>
		<h4>Memory Used</h4>
	</td>
	<td class="red" id="tab-files">
		<var>{$fileCount} Files</var>
		<h4>Included</h4>
	</td>
</tr>
</table>
PQPTABS;

echo '<div id="pqp-console" class="pqp-box">';

if($logCount == 0) {
	echo '<h3>This panel has no log items.</h3>';
}
else {
	echo '<table class="side" cellspacing="0">
		<tr>
			<td class="alt1"><var>'.$output['logs']['logCount'].'</var><h4>Logs</h4></td>
			<td class="alt2"><var>'.$output['logs']['errorCount'].'</var> <h4>Errors</h4></td>
		</tr>
		<tr>
			<td class="alt3"><var>'.$output['logs']['memoryCount'].'</var> <h4>Memory</h4></td>
			<td class="alt4"><var>'.$output['logs']['speedCount'].'</var> <h4>Speed</h4></td>
		</tr>
		</table>
		<section class="main">
			<table cellspacing="0">';

		$class = '';
		foreach($output['logs']['console'] as $key => $log) {
			if (!empty($log['query'])) {
				$log['type'] = 'query';
			}
			echo '<tr class="log-'.$log['type'].'">
				<td class="type">'.$log['type'].'</td>
				<td class="'.$class.'">';

			if($log['type'] == 'log' || $log['type'] == 'query' || $log['type'] == 'speed') {
				echo '<div><pre class="time">'.$log['time'].'</pre> <em>'.$log['message'].'</em></div>';
			}
			elseif($log['type'] == 'memory') {
				echo '<div><pre class="memory">'.$log['memory'].'</pre>';
				if ($log['dataType'] != 'NULL') {
					echo '<em>'.$log['dataType'].'</em>: ';
				}
				echo $log['message'].' </div>';
			}
			elseif($log['type'] == 'error') {
				echo '<div>';
				if (isset($log['duplicate'])) {
					echo '<em>'.$log['duplicate'].' times</em> ';
				}
				echo $log['message'].' <pre>'.$log['file'].':'.$log['line'].'</pre></div>';
				if ($log['context']) {
					echo '<div class="context">'.$log['context'].'</div>';
				}
			}

			echo '</td></tr>';
			if($class == '') $class = 'alt';
			else $class = '';
		}

		echo '</table></section>';
}

echo '</div>';

echo '<div id="pqp-speed" class="pqp-box">';

if($output['logs']['speedCount'] == 0) {
	echo '<h3>This panel has no log items.</h3>';
}
else {
	echo '<table class="side" cellspacing="0">
			<tr><td><var>'.$output['speedTotals']['total'].'</var><h4>Load Time</h4></td></tr>
			<tr><td class="alt"><var>'.$output['speedTotals']['allowed'].'</var> <h4>Max Execution Time</h4></td></tr>
		 </table>
		<section class="main">
			<table cellspacing="0">';

		$class = '';
		foreach($output['logs']['console'] as $log) {
			if($log['type'] == 'speed' || $log['type'] == 'log') {
				if ($log['query']) {
					$class = 'query';
				}
				echo '<tr class="log-'.$log['type'].'">
				<td class="'.$class.'">';
				echo '<div><pre>'.$log['time'].'</pre>'.$log['message'].'</div>';
				if ($log['context']) {
					echo '<div class="context">'.$log['context'].'</div>';
				}
				echo '</td></tr>';
				if($class == '') $class = 'alt';
				else $class = '';
			}
		}

		echo '</table></section>';
}

echo '</div>';

echo '<div id="pqp-queries" class="pqp-box">';

if($output['queryTotals']['count'] == 0) {
	echo '<h3>This panel has no log items.</h3>';
}
else {
	echo '<table class="side" cellspacing="0">
			<tr><td><var>'.$output['queryTotals']['count'].'</var><h4>Total Queries</h4></td></tr>
			<tr><td class="alt"><var>'.$output['queryTotals']['time'].'</var> <h4>Total Time</h4></td></tr>
			<tr><td id="find-duplicate"><var>'.$output['queryTotals']['duplicate'].'</var> <h4>Duplicates</h4></td></tr>
		 </table>
		<section class="main">
			<table cellspacing="0">';

		$class = '';
		foreach($output['queries'] as $query) {
			if (isset($query['duplicate'])) {
				$class = 'duplicate';
			}
			echo '<tr class="log-query">
				<td class="'.$class.'">
					<span>#'.$query['id'].'</span> '.$query['sql'];
			if($query['explain']) {
					echo '<em>
						Possible keys: <b>'.$query['explain']['possible_keys'].'</b> &middot;
						Key Used: <b>'.$query['explain']['key'].'</b> &middot;
						Type: <b>'.$query['explain']['type'].'</b> &middot;
						Rows: <b>'.$query['explain']['rows'].'</b> &middot;
						Speed: <b>'.$query['time'].'</b>
					</em>';
			}
			echo '</td></tr>';
			if($class == '') $class = 'alt';
			else $class = '';
		}

		echo '</table></section>';
}

echo '</div>';

echo '<div id="pqp-memory" class="pqp-box">';

if($output['logs']['memoryCount'] == 0) {
	echo '<h3>This panel has no log items.</h3>';
}
else {
	echo '<table class="side" cellspacing="0">
			<tr><td><var>'.$output['memoryTotals']['used'].'</var><h4>Used Memory</h4></td></tr>
			<tr><td class="alt"><var>'.$output['memoryTotals']['total'].'</var> <h4>Total Available</h4></td></tr>
		 </table>
		<section class="main">
			<table cellspacing="0">';

		$class = '';
		foreach($output['logs']['console'] as $log) {
			if (isset($log['query']) && !empty($log['query'])) {
				continue;
			}
			if($log['type'] == 'memory' || $log['type'] == 'log') {
				echo '<tr class="log-'.$log['type'].'">';
				if ($log['type'] == 'log') {
					$class = 'memory';
				}
				echo '<td class="'.$class.'">';
				if (isset($log['dataType']) && $log['dataType'] != 'NULL') {
					echo '<pre>'.$log['memory'].'</pre> <em>'.$log['dataType'].'</em>: ';
				} else {
					echo '<pre>'.$log['memory'].'</pre> ';
				}
				if (isset($log['context'])) {
					echo '<div class="context">'.$log['context'].'</div>';
				}
				echo $log['message'].'</td>';
				echo '</tr>';
				if($class == '') $class = 'alt';
				else $class = '';
			}
		}

		echo '</table></section>';
}

echo '</div>';

echo '<div id="pqp-files" class="pqp-box">';

if($output['fileTotals']['count'] == 0) {
	echo '<h3>This panel has no log items.</h3>';
}
else {
	echo '<table class="side" cellspacing="0">
				<tr><td><var>'.$output['fileTotals']['count'].'</var><h4>Total Files</h4></td></tr>
			<tr><td class="alt"><var>'.$output['fileTotals']['size'].'</var> <h4>Total Size</h4></td></tr>
			<tr><td><var>'.$output['fileTotals']['largest'].'</var> <h4>Largest</h4></td></tr>
		 </table>
		<section class="main">
			<table cellspacing="0">';

		$class ='';
		foreach($output['files'] as $file) {
			echo '<tr><td class="'.$class.'"><pre>'.$file['size'].'</pre> '.$file['path'].'</td></tr>';
			if($class == '') $class = 'alt';
			else $class = '';
		}

		echo '</table></section>';
}

echo '</div>';

echo <<<HEADER
	<header id="pqp-header" cellspacing="0">
		<h2 class="credit">
			<a href="http://particletree.com" target="_blank">
			<strong>PHP</strong>
			<b class="green">Q</b><b class="blue">u</b><b class="purple">i</b><b class="orange">c</b><b class="red">k</b>
			Profiler</a></h2>
		<div class="actions">
			<a id="toggle-details">
				<img src="/images/material/close.svg" width="16" height="16" alt="close"/>
			</a>
			<a id="toggle-height" class="heightToggle">
				<img class="max" src="/images/material/window-maximize.svg" width="16" height="16" alt="maximize"/>
				<img class="res" src="/images/material/window-restore.svg" width="16" height="16" alt="restore"/>
			</a>
		</div>
	</header>
HEADER;

echo '</div></div>';

echo <<<JAVASCRIPT
<!-- JavaScript -->
<script type="text/javascript">
typeof window.pQp != 'function' ? window.pQp = (function() {
	var PQP_DETAILS = sessionStorage.getItem('PQP_DETAILS') == 'true';
	var PQP_HEIGHT = sessionStorage.getItem('PQP_HEIGHT') || 'short';
	var PQP_LAST_TAP = sessionStorage.getItem('PQP_LAST_TAP') || 'console';
	var PQP_DUP_IDX = 0;

	var pQp;
	var container;

	addEvent(window, 'load', loadCSS);

	function main(initialize) {
		pQp = document.getElementById('pQp');
		container = document.getElementById('pqp-container');
		container.style.display = 'block';

		addEvent('tab-console', 'click', changeTab.bind({}, 'console', false));
		addEvent('tab-speed', 'click', changeTab.bind({}, 'speed', false));
		addEvent('tab-queries', 'click', changeTab.bind({}, 'queries', false));
		addEvent('tab-memory', 'click', changeTab.bind({}, 'memory', false));
		addEvent('tab-files', 'click', changeTab.bind({}, 'files', false));
		addEvent('toggle-details', 'click', toggleDetails);
		addEvent('toggle-height', 'click', toggleHeight);
		addEvent('find-duplicate', 'click', findDuplicate);

		if (!initialize) {
			PQP_DUP_IDX = 0;
			PQP_DETAILS = !PQP_DETAILS;
			PQP_HEIGHT = PQP_HEIGHT == 'short' ? 'tall' : 'short';
		}

		changeTab(PQP_LAST_TAP, true);
		toggleDetails();
		toggleHeight();
	}

	function changeTab(tab, notopen) {
		hideAllTabs();
		addClassName(pQp, tab, true);
		if (!PQP_DETAILS && !notopen) {
			toggleDetails();
		}
		sessionStorage.setItem('PQP_LAST_TAP', PQP_LAST_TAP = tab);
	}

	function hideAllTabs() {
		removeClassName(pQp, 'console');
		removeClassName(pQp, 'speed');
		removeClassName(pQp, 'queries');
		removeClassName(pQp, 'memory');
		removeClassName(pQp, 'files');
	}

	function toggleDetails() {
		if (PQP_DETAILS) {
			addClassName(container, 'hideDetails', true);
			PQP_DETAILS = false;
		} else {
			removeClassName(container, 'hideDetails');
			PQP_DETAILS = true;
		}
		sessionStorage.setItem('PQP_DETAILS', PQP_DETAILS);
	}

	function toggleHeight() {
		if (PQP_HEIGHT == 'short') {
			addClassName(container, 'tallDetails', true);
			PQP_HEIGHT = 'tall';
		} else {
			removeClassName(container, 'tallDetails');
			PQP_HEIGHT = 'short';
		}
		sessionStorage.setItem('PQP_HEIGHT', PQP_HEIGHT);
	}

	function findDuplicate() {
		var queries = document.getElementById('pqp-queries');
		var finded = queries.querySelectorAll('.duplicate');

		var focus = finded[PQP_DUP_IDX++];
		if (focus) focus.scrollIntoView();
		if (finded.length - 1 < PQP_DUP_IDX) {
			PQP_DUP_IDX = 0;
		}
	}

	function loadCSS() {
		var sheet = document.createElement('link');
		sheet.setAttribute('rel', 'stylesheet');
		sheet.setAttribute('type', 'text/css');
		sheet.setAttribute('href', '$cssUrl?r=' + new Date().getTime());
		document.getElementsByTagName('head')[0].appendChild(sheet);
		setTimeout(main, 10);
	}

	function addClassName(objElement, strClass, blnMayAlreadyExist) {
		if (objElement.className) {
			var arrList = objElement.className.split(' ');
			if (blnMayAlreadyExist) {
				var strClassUpper = strClass.toUpperCase();
				for (var i = 0; i < arrList.length; i++) {
					if (arrList[i].toUpperCase() == strClassUpper) {
						arrList.splice(i, 1);
						i--;
					}
				}
			}
			arrList[arrList.length] = strClass;
			objElement.className = arrList.join(' ');
		} else {
			objElement.className = strClass;
		}
	}

	function removeClassName(objElement, strClass) {
		if (objElement.className) {
			var arrList = objElement.className.split(' ');
			var strClassUpper = strClass.toUpperCase();
			for (var i = 0; i < arrList.length; i++) {
				if (arrList[i].toUpperCase() == strClassUpper) {
					arrList.splice(i, 1);
					i--;
				}
			}
			objElement.className = arrList.join(' ');
		}
	}

	function addEvent(obj, type, fn) {
		if (typeof obj != 'object') {
			obj = document.getElementById(obj);
		}

		if (!obj) {
			return;
		}

		if (obj.attachEvent) {
			obj['e' + type + fn] = fn;
			obj[type+fn] = function() {
				obj['e' + type + fn](window.event);
			}
			obj.attachEvent('on' + type, obj[type + fn]);
		} else {
			obj.addEventListener(type, fn, false);
		}
	}

	main.changeTab = changeTab;
	return main;
})() : pQp();
</script>
JAVASCRIPT;

}

?>