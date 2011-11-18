<?php

	/**
	 * Helper functions
	 **/
	function itrim($string) {
		if($string[0] == ':') {
			return substr($string, 1, strlen($string)-1);
		}
		
		return $string;
	}
	
	function parse($data) {
		$data = trim($data);
		
		$arr = explode(' ', $data);
		list($user, $host) = explode('!', $arr[0]);
		
		$message = array_slice($arr, 3);
		$message = implode(' ', $message);
		
		$return = array(
			'ident' => $arr[0],
			'user' => $user, 
			'host' => $host,
			'action' => $arr[1],
			'where' => $arr[2],
			'command' => itrim($arr[3]),
			'message' => itrim($message),
			'data' => $data,
		);
		
		return $return;
	}
	
	function time_ago($timestamp, $granularity=2, $format='Y-m-d H:i:s'){
        $difference = time() - $timestamp;
        if($difference < 0) return '0 seconds ago';
        elseif($difference < 864000){
                $periods = array('week' => 604800,'day' => 86400,'hr' => 3600,'min' => 60,'sec' => 1);
                $output = '';
                foreach($periods as $key => $value){
                        if($difference >= $value){
                                $time = round($difference / $value);
                                $difference %= $value;
                                $output .= ($output ? ' ' : '').$time.' ';
                                $output .= (($time > 1 && $key == 'day') ? $key.'s' : $key);
                                $granularity--;
                        }
                        if($granularity == 0) break;
                }
                return ($output ? $output : '0 seconds').' ago';
        }
        else return date($format, $timestamp);
	}
