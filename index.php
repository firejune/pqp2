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
require_once('classes/PhpQuickProfiler.php');
PhpQuickProfiler::$startTime = PhpQuickProfiler::getMicroTime();
PhpQuickProfiler::$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__).'/');
