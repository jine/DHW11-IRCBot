<?php

	class rules {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			// Failsafe
			if($data['where'] == $this->bot->nick) return;
			
			if($data['command'] == '!regler') {
				$this->bot->privmsg($data['where'], $data['user'].': Du kan hitta våra regler på http://www.dhw11.nu/regler.html');
			}
			
			if($data['command'] == '!rules') {
				$this->bot->privmsg($data['where'], $data['user'].': You can find our rules at http://www.dhw11.nu/regler.html (in Swedish, use google translate!)');
			}
		}
		
	}