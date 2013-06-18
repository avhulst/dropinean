<?php
/* -------------------------------------------
 * MySQL Class
 * von Andreas van Hulst
 * 
 * 
 */
class MyMySQL {
	
	var $host;
	var $db;
	var $user;
	var $port = 3306;
	var $debugger = true;
	
	function __construct () {
		
	}
	
	function debug ($message) {
		if($this->debugger==true) {
			var_dump($message);
		}
	}
	
	
}
?>