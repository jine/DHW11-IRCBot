<?php

	class stats {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			// Failsafe
			if($data['where'] == $this->bot->nick) return;
			
			if($data['command'] == '!stats') {
				$this->bot->privmsg($data['where'], $data['user'].': Please visit http://stats.dhw11.nu/ for the statistics.');
			}
		}
		
	}