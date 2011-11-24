<?php
	
	// General config
	$_CONFIG['debug'] = true;
	
	// Init configs
	error_reporting(E_ALL);				// Debugmode	
	ini_set('memory_limit', '256M');	// Set memory limit so something high.
	ini_set('output_buffering', 0);		// Disables output buffering.
	ini_set('max_execution_time', 0);	// Disables max execution time.
	set_time_limit(0);					// Disables time-limit

    define('BASEPATH', dirname(__FILE__)); 
    define('MODPATH', BASEPATH.'/modules'); 
	chdir(BASEPATH);
	
	// Server config
	$_CONFIG['server'] = 'portlane.se.quakenet.org';
	$_CONFIG['port'] = 6667;
	
	// Bot config
	$_CONFIG['nick'] = 'dhbot';	// Bot nickname
	$_CONFIG['ident'] = 'irc';		// Bot identd
	$_CONFIG['realname'] = 'irc';	// Bot realname
	$_CONFIG['channels'] = '#dhw11,#dreamhack.date'; // Comma separated
	#$_CONFIG['channels'] = '#dhtest'; // Comma separated
	
	// Q-auth for Quakenet
	$_CONFIG['qauth'] = true;			// 1 = enabled
	$_CONFIG['quser'] = 'dhw11';		// Q Auth username
	$_CONFIG['qpass'] = '';	// Q Auth password
	
	// Admin conf
	$_CONFIG['admins'] = array(
		//'nick!user@host' - marks nick at user@host as admin (to control the bot)
		'jine!jine@jine.be', // Jine
	);
