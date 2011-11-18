<?php

	class topic {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			
			
		}
		
	}