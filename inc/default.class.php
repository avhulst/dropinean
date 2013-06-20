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
	 
	 public function insertEAN ($ean, $pid) {
	 	if(strlen($ean)>11 && strlen($pid)>7) {
	 		$lid = $this->insert("insert into ean2id (ean,productid) values ('$ean','$pid') ON DUPLICATE KEY UPDATE productid='$pid'");
			$sql = "SELECT p.name as Name, p.size as Size, a.ean as EAN, p.sku as SKU, p.ident as IDENT
					FROM ean2id as a
					LEFT JOIN productdata p ON a.productid = p.ident
					WHERE a.productid = $pid";
			if($lid>0) {
				$this->query($sql);
				$res = $this->fetchRow();
				return "<h2>Neuer Datensatz (" .$res['SKU'] . ")</h2> <p style='font-size: 14pt;'>" .$res['Name'] . " <br>\nGröße:" . $res['Size'] ." <br>\nEAN: $ean</p>\n";
			} else {
				$this->query($sql);
				$res = $this->fetchRow();
				return "<h2>Update Datensatz (" .$res['SKU'] . ")</h2> <p style='font-size: 14pt;'>" .$res['Name'] . " <br>\nGröße:" . $res['Size'] ." <br>\nEAN: $ean</p>\n";
			}
	 	} else {
	 		return "Angaben nicht Vollständig";
	 	}
	 }
	 
	 public function ProductData2DB ($file)
	 {
	 	if (file_exists($file)) {
	 		$xml = simplexml_load_file($file);
	 	} else {
	 		return "Datei ($file) nicht gefunden";
	 	}
		
		/* XML Daten Selectieren & Vorbereiten */
		$path = "/products/product";
		$result =  $xml ->xpath($path);
		
		#echo "Produktzeilen in der XML:".count($result) . "<br>\n";
		$artikelcount = 0;
		for($i=0; $i<count($result); $i++) {
			// Artikel Stammlaten Lesen
   			$g_stock = 0;
   			$sku = (string) $result[$i]->code;
   			$price = (float)$result[$i]->price;
   			$uvp = (float) $result[$i]->uvp;
            $brand = (string) $result[$i]->brand->name;
			$name = (string) $result[$i]->name;
            $backupsizerange = (string) $result[$i]->descriptionhtml;
            $sizerange = (string) $result[$i]->categories->sizerange;
                      
			$res_article = count($result[$i]->model->article);
   			for($a=0; $a<$res_article; $a++) {
   				$artikelcount++;
   				// Artikel IDENT & Size sovie Lager Lesen
   				// mit Stammdaten aufarbeiten und in Datenbank schreiben
   				$size = (string) $result[$i]->model->article[$a]->size;	
     			$lager = (int) $result[$i]->model->article[$a]->stock;
				$ident = (int) $result[$i]->model->article[$a]->id; 
				$ident = str_pad ( $ident, 8, '0', STR_PAD_LEFT ); 
				#echo $name . " - $ident ($size)<br>\n";
				$sql = "INSERT INTO productdata 
						(sku, price, uvp, brand, name, backupsizerange, sizerange, size, lager, ident)
						VALUES
						('$sku', '$price', '$uvp', '$brand', '$name', '$backupsizerange', '$sizerange', '$size', '$lager', '$ident')
						ON DUPLICATE KEY UPDATE
						price = '$price', uvp = '$uvp', lager = '$lager'
						";
				if($lager>0) {
					$this->insert($sql);
				}
						
  			 } //ENDE FOR
		} //ENDE FOR
	 }
 }
 
 
?>