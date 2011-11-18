<?php

	class qauth {
		private $bot;
		
		function __construct() {
		
			$this->bot = IRCBot::instance();
			if(!$this->bot->qauth) return;
			
			// Don't rush things.
			sleep(3);
			
			// Auth!
			$this->bot->privmsg('Q@CServe.quakenet.org', 'AUTH '.$this->bot->quser.' '.$this->bot->qpass);
			
			// Don't rush things.
			sleep(3);
			
			// Set mode +x
			$this->bot->send_raw('MODE '.$this->bot->nick.' +x');
		}
		
	}