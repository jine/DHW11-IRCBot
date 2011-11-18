<?php

	class caps {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			// Failsafe
			if($data['where'] == $this->bot->nick) return;
			
			$len = strlen($data['message']);
			$times = preg_match_all('/[A-Z]/', $data['message'], $matches);
			$percentage = ($times/$len)*100;
			
			// If percentage is more then 80% of the text
			if($percentage >= 70 && $len > 20) {
				
				$this->bot->privmsg($data['where'], $data['user'].': You\'re violating our rules, please read http://www.dhw11.nu/regler.html (Stop abusing caps lock!)');
			
			}
			
		}
		
	}