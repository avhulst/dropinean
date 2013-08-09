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
                
                //Ean direkt in Ean Tabelle eintragen
                $this->SkuEanAddOne($pid,$ean);
                 
				return "<h2>Neuer Datensatz (" .$res['SKU'] . ")</h2> <p style='font-size: 14pt;'>" .$res['Name'] . " <br>\nGröße:" . $res['Size'] ." <br>\nEAN: $ean</p>\n";
			} else {
				$this->query($sql);
				$res = $this->fetchRow();
                
                //Ean direkt in Ean Tabelle Updaten
                $this->SkuEanAddOne($pid,$ean);
                
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

	/* SKU <-> EAN Tabelle erstellen 
	 * Alle Größen erhalten eine eigene SKU und eine zugehörige EAN
	 * Wichtig für Amazon & Co
	 * */
	public function SkuEanTable () 
	{
		//ean2id tabelle lesen
		$sql = "SELECT * FROM ean2id";
		$this->query($sql);
		$res = $this->fetchRows();	
		
		foreach ($res as $val)
		{
			#echo $val['ean']." - ".$val['productid']." <br>\n";
			
			$this->query('SELECT sku,size FROM productdata WHERE ident = ' . $val['productid']);
			$prod = $this->fetchRow();
			if($prod['size'])
			{
				$sku = $prod['sku'] . "-" . $prod['size'];				
			}
			
			if($val['productid'] && $val['ean'])
			{
				#echo "$sku - ".$val['ean']."<br>\n";
				$this->insert("INSERT INTO ean (ean,sku) VALUES ('".$val['ean']."','".$sku."') ON DUPLICATE KEY UPDATE sku='".$sku."'");
			}
				
		}
		
	} 
	
	/* SKU <-> EAN einzeln eintragen
     * z.B. wärend dem scannen.
     */
	public function SkuEanAddOne ($ident,$ean) 
    {
         $this->query('SELECT sku,size FROM productdata WHERE ident = ' . $ident);
         $prod = $this->fetchRow();
         if($prod['size'])
         {
             $sku = $prod['sku'] . "-" . $prod['size'];              
         }
         
         if($ident && $ean)
         {
             #echo "$sku - ".$val['ean']."<br>\n";
             $this->insert("INSERT INTO ean (ean,sku) VALUES ('".$ean."','".$sku."') ON DUPLICATE KEY UPDATE sku='".$sku."'");
         }        
    }
 }
 
 class snippets 
 {     
    public function TrenneStrasseNr ($street) 
    {
    $streetar = preg_split('/[0-9]/', $street);
    $streetnr = str_replace($streetar[0], "", $street);
    return array("Name" => $this->BereinigeStrassenNamen($streetar[0]), "Nummer" => $this->BereinigeStrassenNamen($streetnr));
    }
    
    public function EntferneLeerzeichenAmSrtingEnde ($str)
    {
        while (substr($str, -1 ) == " ") {
            $str = substr($str, 0, -1);
        }
        
        return $str;
    }

    public function EntferneLeerzeichenAmSrtingAnfang ($str)
    {
        while (substr($str,0, 1) == " ") {
            $str = substr($str, 1);
        }
        
        return $str;
    }
    
    public function BereinigeStrassenNamen ($str)
    {
        $str = $this->EntferneLeerzeichenAmSrtingAnfang($str);
        $str = $this->EntferneLeerzeichenAmSrtingEnde($str);
        return $str;
    }    
    
    /* Datum 
     * Eingabe $ts = mktime($stunde,$minute,$sekunde,$monat,$tag,$jahr);  
     */
    public function Datum ($Stunde="",$Minute="",$Sekunde="",$Monat="",$Tag="",$Jahr="",$Zeitzone="Europe/Berlin",$Sprache="de_DE") 
    {
        setlocale(LC_TIME, "de_DE");  
        date_default_timezone_set($Zeitzone);
        if($Stunde && $Minute && $Sekunde && $Monat && $Tag && $Jahr)    
        {
            $Datum = mktime($Stunde,$Minute,$Sekunde,$Monat,$Tag,$Jahr);
            $MySQL_Datum = date('Y-m-d H:i:s', $Datum);
        }
        else { $MySQL_Datum = ""; }
        
        $MySQL_Datum_Now = date('Y-m-d H:i:s'); 
        $Datum1 = date('d.m.Y');
        $Datum2 = date('D.m.Y');
        $Datum3 = strftime("%A %B %Y");
        
        $Datum_Zeit1 = date('d.m.Y H:i:s'); 
        
        $ret = array("MySQL_Datum" =>$MySQL_Datum,
                     "MySQL_Datum_Now" => $MySQL_Datum_Now,
                     "Datum1" => $Datum1,
                     "Datum2" => $Datum2,
                     "Datum3" => $Datum3,
                     "Datum_Zeit1" => $Datum_Zeit1);
        
        return $ret;   
    } 
 }
 
?>