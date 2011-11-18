<?php

	class protection {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			// Failsafe
			if($data['where'] == $this->bot->nick) return;
			
			$hash = md5($data['data']);
			$cache = $this->bot->memcache->get($hash);
			
			// No such record exists
			if(!$cache) {
				// Store record
				$this->bot->memcache->set($hash, 1, 180);

			} elseif($cache <= 2) {
				// Increment by one
				$this->bot->memcache->increment($hash, 1);

			} elseif($cache >= 3 && $cache <= 5) {
				// Increment by one and warn
				$this->bot->memcache->set($hash, $cache+1, 300);
				
				// Send notice
				$this->bot->privmsg($data['where'], $data['user'].': You\'re violating our rules, please read http://www.dhw11.nu/regler.html!');

			} elseif($cache >= 6 && $cache <= 7) {
				// Increment by one and warn
				$this->bot->memcache->set($hash, $cache+1, 600);
				
				// Send notice
				$this->bot->kick($data['user'], $data['where'], 'You\'re violating our rules, please read http://www.dhw11.nu/regler.html!');

			} elseif($cache >= 8) {
				// Increment by one and warn
				$this->bot->memcache->set($hash, $cache+1, 5400);
				
				// Send notice
				$this->bot->send_raw('MODE '.$data['where'].' +b *!'.$data['host']);
				$this->bot->kick($data['user'], $data['where'], 'You\'re violating our rules, please read http://www.dhw11.nu/regler.html!');
			}
		}
	}