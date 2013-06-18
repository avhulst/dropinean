<?php
/* -------------------------------------------
 * Drop In Ean
 * 
 * Programm zum nacherfassen von EAN zur Produkt ID
 *  
 * Andreas van Hulst
 * motormord@googlemail.com
 * 
 * -------------------------------------------
 */
 
 require_once('inc/default.class.php');
 
 
 $ean = new DropInEAN();
 
 $ean->connect();
 $ean->query('insert into ean2id (ean,productid) values ("7878787878","123456") ON DUPLICATE KEY UPDATE productid="123458" ');
 $ean->query('select * from ean2id');
 $res = $ean->fetchRows();
 
 #var_dump($res);
 
$ean->disconnect(); 
require_once('template/header.php');

require_once('template/formean.php');

require_once('template/footer.php');
?>