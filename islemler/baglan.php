<?php
$host="localhost";
$veritabani_ismi="serin1";
$kullanici_adi="root";
$sifre="22595261";

try {

	$db=new PDO("mysql:host=$host;dbname=$veritabani_ismi;charset=utf8",$kullanici_adi,$sifre);
	//echo "veritabını bağlantısı başarılı";
	
} 

catch (PDOException $e) {
	echo $e->getMesage();
}


 ?>