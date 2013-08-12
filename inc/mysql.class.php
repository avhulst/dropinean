<?php
/* -------------------------------------------
 * MySQL Class
 * von Andreas van Hulst
 * 
 * 
 */
class MyMySQL {
	
	var $host = "localhost";
	var $db = "dropinean";
	var $user = "root";
	var $pass = "root";
	var $debugger = true;
	
	private $connection = NULL;
  	private $result = NULL;
  	private $counter=NULL;
	
	function __construct () {
	//Schütze vor SQL Injection
	foreach ($_REQUEST as $key => $val) {
  		$_REQUEST["$key"] = mysql_real_escape_string($val);
	}	
	}
	
	function debug ($message) {
		if($this->debugger==true) {
			if(strlen($message)>0)
			{
				var_dump($message);
			}
		}
	}
	
	public function connect () {
		$this->connection = mysql_connect($this->host, $this->user, $this->pass);
		mysql_select_db($this->db);
		if ($this->connection) { return true; } else { return false; }
	}
	
  	public function disconnect() {
    	if (is_resource($this->connection))				
        	mysql_close($this->connection);
  	}
	
 	public function query($query) {
  		$this->result=mysql_query($query,$this->connection);
  		$this->counter=NULL;		
	}
	
	public function insert($query) {
  		$this->result=mysql_query($query,$this->connection);
		$this->debug(mysql_error());
  		return mysql_insert_id();		
	}
	
  	public function fetchRow() {
  		return mysql_fetch_assoc($this->result);
  	}
	
	public function fetchRows() {
		while ($row = mysql_fetch_assoc($this->result)) {
			$rows[] = $row;
		}
		return $rows;
	}

	public function count() {
		if($this->counter==NULL && is_resource($this->result)) {
  			$this->counter=mysql_num_rows($this->result);
  		}
		return $this->counter;
	}

}	
?>