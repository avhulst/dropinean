<?php
require_once('inc/default.class.php');

$ean2id = new DropInEAN();

$ean2id->ProductData2DB('tmp/ProductData.xml');

?>