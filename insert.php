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
 
 $res = $ean2id->insertEAN($ean,$pid);
 
 echo '<div id="result">'.$res.'</div>';
 $ean2id->disconnect(); 
 
 ?>
 