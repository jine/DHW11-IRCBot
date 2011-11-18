<?php

	class lastseen {
		
		private $bot;
		
		function __construct() {
			$this->bot = IRCBot::instance();
		}
		
		function PRIVMSG($data) {
			$data = parse($data);
			
			// Failsafe
			if($data['where'] == $this->bot->nick) return;
			
			if($data['command'] == '!lastseen') {
				list($command, $who) = explode(' ', $data['message']);
				if(empty($who)) {
					$this->bot->privmsg($data['where'], $data['user'].': Correct syntax is !lastseen *nick*');
					return;
				}
				
				$cache = $this->bot->memcache->get('last_seen-'.md5($who));
				if(!empty($cache)) {
					$this->bot->privmsg($data['where'], $data['user'].': '.$who.' was last seen '.time_ago($cache).'.');
				} else {
					$this->bot->privmsg($data['where'], $data['user'].': I don\'t have any record of '.$who.' being here...');
				}
			
			}
			
			// And log the activity.
			$this->bot->memcache->set('last_seen-'.md5($data['user']), time(), time()+(60*60*24*14));
				
		}
		
	}