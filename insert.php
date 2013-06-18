<?php
/* -------------------------------------------
 * Drop In Ean
 * 
 * Programm zum nacherfassen von EAN zur Produkt ID
 *  
 * Andreas van Hulst
 * motormord@googlemail.com
 * 
 * file: insert.php
 * 
 * Eintragen einer neuen EAN und ProductID, werte vie $_REQUEST
 * -------------------------------------------
 */
 
 require_once('inc/default.class.php');
 
 
 $ean2id = new DropInEAN();
 //Erst nach dem Starten der Klasse sind die $_REQUEST von SQL Injection bereinigt.
 $ean = $_REQUEST['ean'];
 $pid = $_REQUEST['pid'];
 
 $ean2id->connect();
 
 $lid = $ean2id->insert("insert into ean2id (ean,productid) values ('$ean','$pid') ON DUPLICATE KEY UPDATE productid='$pid'");
 
 echo '<div id="result">LETZE ID: '.$lid.'</div>';
 $ean2id->disconnect(); 
 
 ?>
 