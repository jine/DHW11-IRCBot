<?php

class IRCBot {
	
	// Internal vars
	private static $instance;
	public $memcache;
	
	// Internal storage
	private $data = array(); // bot config and variables
	private $modules = array(); // modules and module methods
	private $socket; // IRC connection
		
	private function __construct($_CONFIG) {
		$this->debug('IRCBot __construct running, loading vars and config');
		
		// Overloading all config into class.
		foreach($_CONFIG as $name => $value) {
			$this->$name = $value;
		}
		
		// Connect to memcached server
		$this->debug('Connecting to memcached server.');
		$this->memcache = new Memcached('ircbot');
		$this->memcache->addServer('127.0.0.1', 11211);
	
	}
	
	public static function instance($_CONFIG = array()) {
	
		if(!self::$instance) {
			self::$instance = new IRCBot($_CONFIG);
		}
		
		return self::$instance;
	}

	public function connect() {
		$this->debug('Connecting to IRC server: '.$this->server.':'.$this->port);
		
		// Init socket etc.
		$this->socket = fsockopen($this->server, $this->port, $err_num, $err_msg, 30);
		if(!$this->socket) {
			$this->debug('Connection to server failed: '.$err_msg);
		}
		
		// Sending NICK/PASS/USER to server
		$this->send_raw(sprintf('NICK %s', $this->nick));
		$this->send_raw(sprintf('PASS %s', $this->nick));
		$this->send_raw(sprintf('USER %s %s %s :%s', $this->ident, $this->ident, $this->server, $this->realname));
		
		// .. and wait for ping
		$this->debug('Wait for first PING to arrive.');
		while($data = fgets($this->socket, 4096)) {
			
			if(substr($data, 0, 4) == 'PING') {
				$this->pingpong($data);
				break;
			}
		}
		
		// Wait and flush socket to be able to connect
		fflush($this->socket);
		sleep(10);
	}
	
	public function is_connected() {
		if($this->socket) {
			return true;
		}
		
		return false;
	}
	
	public function load_module($module, $args = array()) {
		$this->debug('Loading module: '.$module.' with args: '.str_replace("\n", '', var_export($args, true)));
		
		// Load module and add to $modules etc
		if(is_file(MODPATH.'/'.$module.'.php')) {
			include_once(MODPATH.'/'.$module.'.php');
		}
		
		if (class_exists($module)) {
			$this->modules[$module] = new $module;
		} else {
			$this->debug('Couldn\'t find class for module: '.$module);
		}
	}
	
	function send_raw($string) {
		$this->debug('Sending '.$string.' to IRC server.');
		
		// Send to server
		if(!fwrite($this->socket, $string.PHP_EOL)) {
			$this->debug('Couldn\'t write '.$string.' to connection, exiting...');
			die();
		}
		
	}
	
	// Special function to handle ping/pongs
	function pingpong($data) {
		// Got PING, sending PONG
		$this->debug('Got '.$data);
		
		// Send PONG
		$ping = explode(' ', $data);
		$this->send_raw('PONG '.trim($ping[1]));
	}
	
	// Shortcut to send PRIVMSG
	function privmsg($who, $what) {
		$this->send_raw(sprintf('PRIVMSG %s :%s', $who, $what));	
	}
	// Shortcut to send NOTICES
	function notice($who, $what) {
		$this->send_raw(sprintf('NOTICE %s :%s', $who, $what));	
	}
	
	// Shortcut to kick people
	function kick($who, $from, $reason) {
		$this->send_raw(sprintf('KICK %s %s :%s', $from, $who, $reason));	
	}
	
	
	
	/**************************
	** Main loop begins here! *
	***************************/
	public function loop() {
		$this->debug('Waiting for commands in loop()');
		
		// Join channels
		$this->send_raw('JOIN '.$this->channels);
		
		// Build a method tree using the loaded classes.
		$tree = array();
		foreach($this->modules as $module) {
			$methods = get_class_methods($module);
			
			foreach($methods as $method) {
				
				// Ignore constructor
				if($method == '__construct') continue;
				
				$tree[$method][] = get_class($module);
			}
		}
		
		// Ennnnnnndless loop!
		while(true) {
			
			while($data = fgets($this->socket, 4096)) {
			
				// Remove : if it's the first char
				$data = itrim($data);
				
				// Special case for ping/pong
				if(substr($data, 0, 4) == 'PING') {
					$this->pingpong($data);
				}
				
				// Set som vars to reflect the input string
				$junk = $action = '';
				list($junk, $action) = explode(' ', $data);
				
				// Check if the action exists (privmsg, notice etc)
				if(isset($tree[$action])) {
				
					// Loop through all classes in the the tree for that action
					foreach($tree[$action] as $class) {
					
						// Run the action in the module class
						$this->modules[$class]->$action($data);
					
					}			
				
				}
				
			}
			
			// Sleep for 100ms
			usleep(100000);
		}
	}
	
	
	
	
	/***************************
	* Logging and debug methods
	****************************/
	public function debug($message) {
		
		// Trim a bit...
		$message = trim($message);
		
		// Always log everything
		$this->__log($message);
		
		// If debug is turned on, echo message
		if($this->debug) {
			echo '['.date('Y-m-d H:i:s').'] '.$message.PHP_EOL;
		}
		
	}
	
	private function __log($message) {
		$log = '['.date('Y-m-d H:i:s').'] '.$message.PHP_EOL;
		file_put_contents(BASEPATH.'/bot.log', $log, FILE_APPEND | LOCK_EX);
	}
	
	/**********************
	 * Magic methods below
	 **********************/
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		
		return null;
	}

	public function __isset($name) {
		return isset($this->data[$name]);
	}

	public function __unset($name) {
		unset($this->data[$name]);
	}
}