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
	 	if(strlen($ean)>10 && strlen($pid)>4) {
	 		$lid = $this->insert("insert into ean2id (ean,productid) values ('$ean','$pid') ON DUPLICATE KEY UPDATE productid='$pid'");
			if($lid>0) {
				$this->query("SELECT * FROM productdata WHERE ident = $pid");
				$res = $this->fetchRow();
				return "<h2>Neuer Datensatz</h2> <p>" .$res['name'] . " Größe:" . $res['size'] ."</p>";
			} else {
				$this->query("SELECT * FROM productdata WHERE ident = $pid");
				$res = $this->fetchRow();
				return "<h2>Update Datensatz</h2> <p>" .$res['name'] . " Größe:" . $res['size'] ."</p>";
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
   				// Artikel IDENT & Size sovie Lager Lesen
   				// mit Stammdaten aufarbeiten und in Datenbank schreiben
   				$size = (string) $result[$i]->model->article[$a]->size;	
     			$lager = (int) $result[$i]->model->article[$a]->stock;
				$ident = (int) $result[$i]->model->article[$a]->id;  
				#echo $name . " - $ident ($size)<br>\n";
				$sql = "INSERT INTO productdata 
						(sku, price, uvp, brand, name, backupsizerange, sizerange, size, lager, ident)
						VALUES
						('$sku', '$price', '$uvp', '$brand', '$name', '$backupsizerange', '$sizerange', '$size', '$lager', '$ident')
						ON DUPLICATE KEY UPDATE
						price = '$price', uvp = '$uvp', lager = '$lager'
						";
						$this->insert($sql);
						
  			 } //ENDE FOR
		} //ENDE FOR
	 }
 }
 
 
?>