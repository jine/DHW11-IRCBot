<?php

	class commands {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			// Failsafe
			if($data['where'] == $this->bot->nick) return;
			
			if($data['command'] == '!kommandon') {
				$this->bot->privmsg($data['where'], $data['user'].': !regler, !kommandon, !lastseen, !stats');
			}
			
			if($data['command'] == '!commands') {
				$this->bot->privmsg($data['where'], $data['user'].': !rules, !commands, !lastseen, !stats');			}
		}
		
	}