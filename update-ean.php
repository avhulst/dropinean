<?php
/* Lesen der EAN's aus ean2id
 * zusammenfügen in ean (mit sku)
 * 
 * 
 */
require_once('inc/default.class.php');

$ean2id = new DropInEAN();

$ean2id->SkuEanTable();

?>