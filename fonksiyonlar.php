<?php

function turkce_temizle($metin) {
	$turkce=array("ş","Ş","ı","ü","Ü","ö","Ö","ç","Ç","ş","Ş","ı","ğ","Ğ","İ","ö","Ö","Ç","ç","ü","Ü");
	$duzgun=array("s","S","i","u","U","o","O","c","C","s","S","i","g","G","I","o","O","C","c","u","U");
	$metin=str_replace($turkce,$duzgun,$metin);
	$metin = preg_replace("@[^a-z0-9\-_şıüğçİŞĞÜÇ]+@i","-",$metin);
	$yeniisim = mb_strtolower($metin, 'utf8');
	return $yeniisim;
};


function tum_bosluk_sil($veri)
{
	return str_replace(" ", "", $veri); 
};

function yetkikontrol() {
	if (empty($_SESSION['kul_mail'])) {
		$kul_mail="x";
	} else {
		$kul_mail=$_SESSION['kul_mail'];
	}
	
	include 'islemler/baglan.php';
	$yetki=$db->prepare("SELECT kul_yetki FROM kullanicilar where session_mail=:session_mail");
	$yetki->execute(array(
		'session_mail' => $kul_mail
	));
	$yetkicek=$yetki->fetch(PDO::FETCH_ASSOC);

	if ($yetkicek['kul_yetki']==1) {
		$sonuc="yetkili";
		return $sonuc;
	} else {
		$sonuc="yetkisiz";
		return $sonuc;
	}
};

function oturumkontrol() {
	include 'islemler/baglan.php';
	if (empty($_SESSION['kul_mail']) or empty($_SESSION['kul_id'])) {
		header("location:login.php?durum=izinsiz");
		exit;
	} else {

		$kullanici=$db->prepare("SELECT * FROM kullanicilar where session_mail=:session_mail");
		$kullanici->execute(array(
			'session_mail' => $_SESSION['kul_mail']
		));

		$say=$kullanici->rowcount();
		$kullanicicek=$kullanici->fetch(PDO::FETCH_ASSOC);
		if ($say==0) {
			header("location:login.php?durum=izinsiz");
			exit;
		}
	}	
};

function guvenlik($gelen){
	$giden = addslashes($gelen);
	$giden = htmlspecialchars($giden);
	$giden = htmlentities($giden);
	$giden = strip_tags($giden);
	return $giden;
};

function fnk(){
	echo "<script language=javascript>document.write(unescape('%3c%66%6f%6f%74%65%72%20%63%6c%61%73%73%3d%22%73%74%69%63%6b%79%2d%66%6f%6f%74%65%72%20%62%67%2d%77%68%69%74%65%22%3e%0a%09%3c%64%69%76%20%63%6c%61%73%73%3d%22%63%6f%6e%74%61%69%6e%65%72%20%6d%79%2d%61%75%74%6f%22%3e%0a%09%09%3c%64%69%76%20%63%6c%61%73%73%3d%22%63%6f%70%79%72%69%67%68%74%20%74%65%78%74%2d%63%65%6e%74%65%72%20%6d%79%2d%61%75%74%6f%22%3e%0a%09%09%09%3c%73%70%61%6e%3e%43%6f%70%79%72%69%67%68%74%20%26%63%6f%70%79%3b%20%3c%61%20%68%72%65%66%3d%22%64%6f%67%61%63%61%6e%22%3e%44%4f%47%41%43%41%4e%3b%3c%2f%61%3e%54%65%6b%6c%69%66%20%54%61%6b%69%70%20%50%61%6e%65%6c%69%3b%3c%2f%73%70%61%6e%3e%0a%09%09%3c%2f%64%69%76%3e%0a%09%3c%2f%64%69%76%3e%0a%3c%2f%66%6f%6f%74%65%72%3e'))</script>
	";
}

function sifreleme($kul_mail) {
	$gizlianahtar = '05a8acd63ecadfc55842804bc537f76e';
	return md5(sha1(md5($_SERVER['REMOTE_ADDR'] . $gizlianahtar . $kul_mail . "MDS" . date('d.m.Y H:i:s') . $_SERVER['HTTP_USER_AGENT'])));
};

?>
