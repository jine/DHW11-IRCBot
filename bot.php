<?php

	require_once('config.php');
	require_once('helpers.php');
	require_once('IRCBot.class.php');
	
	// Init new bot with values from $_CONFIG
	$bot = IRCBot::instance($_CONFIG);
	
	// Make a connection based on config
	$bot->connect();
	
	if($bot->is_connected()) {
		 // Auths with Q when connected, if it's enabled.
		if($bot->qauth) {
			$bot->load_module('qauth');
		}
		
		$bot->load_module('lastseen');
		$bot->load_module('rules');
		$bot->load_module('stats');
		$bot->load_module('topic');
		$bot->load_module('commands');
		$bot->load_module('protection');
		$bot->load_module('caps');
		#$bot->load_module('admin', array('admins' => $_CONFIG['admins']));
		
	}
	
	// Put the bot in a endless loop :)
	$bot->loop();