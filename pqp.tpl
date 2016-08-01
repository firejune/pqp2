<div id="pqp-container" class="pQp" style="display:none">
<div id="pQp" class="console">
	<table id="pqp-metrics" cellspacing="0">
		<tr>
			<td class="green" id="tab-console">
				<var>{$output.logs.console|@count}</var>
				<h4>Console</h4>
			</td>
			<td class="blue" id="tab-speed">
				<var>{$output.speedTotals.total}</var>
				<h4>Load Time</h4>
			</td>
			<td class="purple" id="tab-queries">
				<var>{$output.queryTotals.count} Queries</var>
				<h4>Database</h4>
			</td>
			<td class="orange" id="tab-memory">
				<var>{$output.memoryTotals.used}</var>
				<h4>Memory Used</h4>
			</td>
			<td class="red" id="tab-files">
				<var>{$output.files|@count} Files</var>
				<h4>Included</h4>
			</td>
		</tr>
	</table>

	<div id='pqp-console' class='pqp-box'>
		{if $output.logs.console|@count == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class='side' cellspacing='0'>
			<tr>
				<td class='alt1'><var>{$output.logs.logCount}</var><h4>Logs</h4></td>
				<td class='alt2'><var>{$output.logs.errorCount}</var> <h4>Errors</h4></td>
			</tr>
			<tr>
				<td class='alt3'><var>{$output.logs.memoryCount}</var> <h4>Memory</h4></td>
				<td class='alt4'><var>{$output.logs.speedCount}</var> <h4>Speed</h4></td>
			</tr>
			</table>
			<section class="main">
  			<table cellspacing='0'>
				{foreach from=$output.logs.console item=log}
				  {if $log.query}
  				  {assign var=$log.type value='query'}
				  {/if}
					<tr class='log-{$log.type}'>
						<td class='type'>{$log.type}</td>
						<td class="{cycle values="alt,"}">
							{if $log.type == 'log' || $log.type == 'query' || $log.type == 'speed'}
								<div><pre class="time">{$log.time}</pre> <em>{$log.message}</em></div>
							{elseif $log.type == 'memory'}
								<div>
  								<pre class="memory">{$log.memory}</pre>
  								{if $log.dataType != 'NULL'}<em>{$log.dataType}</em>: {/if}
                  {$log.message}
                </div>
							{elseif $log.type == 'error'}
								<div>
          				{if $log.duplicate}<em>{$log.duplicate} times</em>{/if}
          				{$log.message} <pre>{$log.file}:{$log.line}</pre>
  						  </div>
                {if $log.context}<div class="context">{$log.context}</div>{/if}
							{/if}
						</td>
						</tr>
				{/foreach}
			  </table>
			</section>
		{/if}
	</div>

	<div id="pqp-speed" class="pqp-box">
		{if $output.logs.speedCount == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class='side' cellspacing='0'>
				<tr><td><var>{$output.speedTotals.total}</var><h4>Load Time</h4></td></tr>
				<tr><td class='alt'><var>{$output.speedTotals.allowed} s</var> <h4>Max Execution Time</h4></td></tr>
			</table>
      <section class="main">
			<table cellspacing='0'>
			{foreach from=$output.logs.console item=log}
				{if $log.type == 'speed' || $log.type == 'log'}
				  {if $log.query}
  				  {assign var=$log.type value='query'}
				  {/if}
					<tr class='log-{$log.type}'>
						<td class="{cycle values="alt,"}">
  						<div><pre>{$log.time}</pre> {$log.message}</div>
  						{if $log.context}<div class="context">{$log.context}</div>{/if}
  				  </td>
					</tr>
				{/if}
			{/foreach}
			</table>
      </section>
		{/if}
	</div>

	<div id='pqp-queries' class='pqp-box'>
		{if $queryTotals.count == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class='side' cellspacing='0'>
			<tr><td><var>{$output.queryTotals.count}</var><h4>Total Queries</h4></td></tr>
			<tr><td class='alt'><var>{$output.queryTotals.time}</var> <h4>Total Time</h4></td></tr>
			<tr><td><var>0</var> <h4>Duplicates</h4></td></tr>
			</table>

      <section class="main">
				<table cellspacing='0'>
				{foreach from=$queries item=query}
						<tr>
							<td class="{cycle values="alt,"}">
								<span>#{$query.id}</span> {$query.sql}
								{if $query.explain}
								<em>
									Possible keys: <b>{$query.explain.possible_keys}</b> &middot;
									Key Used: <b>{$query.explain.key}</b> &middot;
									Type: <b>{$query.explain.type}</b> &middot;
									Rows: <b>{$query.explain.rows}</b> &middot;
									Speed: <b>{$query.time}</b>
								</em>
								{/if}
							</td>
						</tr>
				{/foreach}
				</table>
      </section>
		{/if}
	</div>

	<div id="pqp-memory" class="pqp-box">
		{if $output.logs.memoryCount == 0}
			<h3>This panel has no log items.</h3>
		{else}
			<table class='side' cellspacing='0'>
				<tr><td><var>{$output.memoryTotals.used}</var><h4>Used Memory</h4></td></tr>
				<tr><td class='alt'><var>{$output.memoryTotals.total}</var> <h4>Total Available</h4></td></tr>
			</table>

      <section class="main">
			<table cellspacing='0'>
			{foreach from=$output.logs.console item=log}
  			{if !$log.query}
  				{if $log.type == 'memory' || $log.type == 'log'}
  					<tr class='log-{$log.type}'>
  						<td class="{cycle values="alt,"}"><b>{$log.data}</b> <em>{$log.dataType}</em>: {$log.message}</td>
  					</tr>
  				{/if}
  			{/if}
			{/foreach}
			</table>
      </section>
		{/if}
	</div>

	<div id='pqp-files' class='pqp-box'>
			<table class='side' cellspacing='0'>
				<tr><td><var>{$output.fileTotals.count}</var><h4>Total Files</h4></td></tr>
				<tr><td class='alt'><var>{$output.fileTotals.size}</var> <h4>Total Size</h4></td></tr>
				<tr><td><var>{$output.fileTotals.largest}</var> <h4>Largest</h4></td></tr>
			</table>
			<section class="main">
			<table cellspacing='0'>
				{foreach from=$files item=file}
					<tr><td class="{cycle values="alt,"}"><b>{$file.size}</b> {$file.message}</td></tr>
				{/foreach}
			</table>
			</section>
	</div>

	<header id="pqp-header" cellspacing="0">
		<h2 class="credit">
			<a href="http://particletree.com" target="_blank">
			<strong>PHP</strong>
			<b class="green">Q</b><b class="blue">u</b><b class="purple">i</b><b class="orange">c</b><b class="red">k</b>
			Profiler</a></h2>
		<div class="actions">
			<a id="toggle-details">
				<img src="{$path}images/close.svg" width="16" height="16" alt="close"/>
			</a>
			<a id="toggle-height" class="heightToggle">
				<img class="max" src="{$path}images/window-maximize.svg" width="16" height="16" alt="maximize"/>
				<img class="res" src="{$path}images/window-restore.svg" width="16" height="16" alt="restore"/>
			</a>
		</div>
	</header>
</div>
</div>

<!-- JavaScript -->
{literal}
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
		{/literal}sheet.setAttribute('href', '{$path}css/pQp.css');{literal}
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
{/literal}