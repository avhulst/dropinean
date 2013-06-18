<?php
/* -------------------------------------------
 * Default Classes
 * von Andreas van Hulst
 * 
 * Standard Classes für die Anwendung
 * 
 */
 require_once 'mysql.class.php';
 /**
  * 
  */
 class DropInEAN extends MyMySQL {
     
     function __construct() {
         $this->connect();
     }
	 
 }
 
 
?>