<?php

@ob_start();
@session_start();
include 'baglan.php';
include '../fonksiyonlar.php';



$ayarsor=$db->prepare("SELECT * FROM ayarlar");
$ayarsor->execute();
$ayarcek=$ayarsor->fetch(PDO::FETCH_ASSOC);

//Oturum açma girişi//
if (isset($_POST['oturumac'])) {
	$kul_mail=guvenlik($_POST['kul_mail']);
	$kul_sifre=md5($_POST['kul_sifre']);
	$kullanicisor=$db->prepare("SELECT * FROM kullanicilar WHERE kul_mail=:mail and kul_sifre=:sifre");
	$kullanicisor->execute(array(
		'mail'=> $kul_mail,
		'sifre'=> $kul_sifre
	));
	$sonuc=$kullanicisor->rowCount();
	if ($sonuc==1) {
		$kullanicicek=$kullanicisor->fetch(PDO::FETCH_ASSOC);
		$_SESSION['kul_mail']=sifreleme($kul_mail);
		$_SESSION['kul_id']=$kullanicicek['kul_id'];

		$ipkaydet=$db->prepare("UPDATE kullanicilar SET
			ip_adresi=:ip_adresi, 
			session_mail=:session_mail WHERE 
			kul_mail=:kul_mail
			");
		$kaydet=$ipkaydet->execute(array(
			'ip_adresi' => $_SERVER['REMOTE_ADDR'], //Güvenlik için işlemine karşı kullanıcının ip adresini veritabanına kayıt etme
			'session_mail' => sifreleme($kul_mail),
			'kul_mail' => $kul_mail
		));
		header("location:../index.php");
		exit;
	} else {
		header("location:../login?durum=hata");
	}
	exit;
}


/*Oturum Açma İşlemi Giriş*/

if (isset($_POST['genelayarkaydet'])) {
  if (yetkikontrol()!="yetkili") {
    header("location:../index.php");
    exit;
  }
 			$boyut = $_FILES['site_logo']['size'];//Dosya boyutumuzu alıp değişkene aktardık.
            if($boyut > 3145728)//Burada dosyamız 3 mb büyükse girmesini söyledik
            {
            //İsteyen arkadaslar burayı istediği gibi değiştirebilir size kalmış bir şey
                echo 'Dosya 3MB den büyük olamaz.';// 3 mb büyükse ekrana yazdıracağımız alan
              } else {

               if ($boyut < 20) {
                $genelayarkaydet=$db->prepare("UPDATE ayarlar SET
                 site_baslik=:baslik,
                 site_aciklama=:aciklama,
                 site_sahibi=:sahip,
                 mail_onayi=:mail_onayi,
                 duyuru_onayi=:duyuru_onayi where id=1
                 ");

                $ekleme=$genelayarkaydet->execute(array(
                 'baslik' => guvenlik($_POST['site_baslik']),
                 'aciklama' => guvenlik($_POST['site_aciklama']),
                 'sahip' => guvenlik($_POST['site_sahibi']),
                 'mail_onayi' => guvenlik($_POST['mail_onayi']),
                 'duyuru_onayi' => guvenlik($_POST['duyuru_onayi'])
               ));

  			   } else {

                $yuklemeklasoru = '../img';
                @$gecici_isim = $_FILES['site_logo']["tmp_name"];
                @$dosya_ismi = $_FILES['site_logo']["name"];
            		$benzersizsayi1=rand(100,10000); //Güvenlik için yüklenen dosyanın başına rastgele karakterler koyuyoruz
            		$benzersizsayi2=rand(100,10000); //Güvenlik için yüklenen dosyanın başına rastgele karakterler koyuyoruz
            		$isim=$benzersizsayi1.$benzersizsayi2.$dosya_ismi;
            		$resim_yolu=substr($yuklemeklasoru, 3)."/".tum_bosluk_sil($isim);
            		@move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim");

            		$genelayarkaydet=$db->prepare("UPDATE ayarlar SET
            			site_baslik=:baslik,
            			site_aciklama=:aciklama,
            			site_sahibi=:sahip,
            			mail_onayi=:onay,
            			duyuru_onayi=:duyuru_onayi,
            			site_logo=:site_logo where id=1
            			");

            		$ekleme=$genelayarkaydet->execute(array(
            			'baslik' => guvenlik($_POST['site_baslik']),
            			'aciklama' => guvenlik($_POST['site_aciklama']),
            			'sahip' => guvenlik($_POST['site_sahibi']),
            			'onay' => guvenlik($_POST['mail_onayi']),
            			'duyuru_onayi' => guvenlik($_POST['duyuru_onayi']),
            			'site_logo' => $resim_yolu
            		));
            	}
            }

            if ($ekleme) {
            	header("location:../ayarlar?durum=ok");
            } else {
            	header("location:../ayarlar?durum=no");
            	exit;
            }            
          }

//Teklif Ekleme Bölümü

if (isset($_POST['projeekle'])) {
            if (yetkikontrol()!="yetkili") {
              header("location:../index.php");
              exit;
            }
			$teklifekle=$db->prepare("INSERT INTO teklif SET 
			teklif_baslik=:baslik,
			teklif_detay=:detay,
			teklif_teslim_tarihi=:teslim_tarihi,
			teklif_durum=:durum,
			teklif_onay=:onay 
			");

			$ekleme=$teklifekle->execute(array(
             'baslik' => guvenlik($_POST['teklif_baslik']),
             'detay' => $_POST['proje_detay'],
             'teslim_tarihi' => guvenlik($_POST['teklif_teslim_tarihi']),
             'durum' => guvenlik($_POST['teklif_durum']),
             'onay' => guvenlik($_POST['teklif_onay'])
           ));

		    if ($_FILES['teklif_dosya']['error']=="0") {
              $yuklemeklasoru = '../dosyalar';
              @$gecici_isim = $_FILES['teklif_dosya']["tmp_name"];
              @$dosya_ismi = $_FILES['teklif_dosya']["name"];
              $benzersizsayi1=rand(100000,999999);
              $isim=$benzersizsayi1.$dosya_ismi;
              $resim_yolu=substr($yuklemeklasoru, 3)."/".tum_bosluk_sil($isim);
              @move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim");   
              $son_eklenen_id=$db->lastInsertId();
              $dosyayukleme=$db->prepare("UPDATE teklif SET
               dosya_yolu=:dosya_yolu WHERE teklif_id=:teklif_id ");

              $yukleme=$dosyayukleme->execute(array(
               'dosya_yolu' => $resim_yolu,
               'teklif_id' => $son_eklenen_id
             ));
            }

             if ($ekleme) {
             header("location:../teklifler?durum=ok");
             exit;
           } else {
             header("location:../teklifler?durum=no");
             exit;
           }
           exit;
         }

         //***teklif güncelle***********


         if (isset($_POST['teklifguncelle'])) {
          if (yetkikontrol()!="yetkili") {
            header("location:../index.php");
            exit;
          }

          $teklifguncelle=$db->prepare("UPDATE teklif SET 
          	teklif_baslik=:baslik,
			teklif_detay=:detay,
			teklif_teslim_tarihi=:teslim_tarihi,
			teklif_durum=:durum,
			teklif_onay=:onay  where teklif_id={$_POST['teklif_id']}");

          $guncelle=$teklifguncelle->execute(array(
            'baslik' => guvenlik($_POST['teklif_baslik']),
            'detay' => $_POST['teklif_detay'],
            'teslim_tarihi' => guvenlik($_POST['teklif_teslim_tarihi']),
            'durum' => guvenlik($_POST['teklif_durum']),
            'onay' => guvenlik($_POST['teklif_onay'])
          ));
          if ($_FILES['teklif_dosya']['error']=="0") {

            $yuklemeklasoru = '../dosyalar';
            @$gecici_isim = $_FILES['teklif_dosya']["tmp_name"];
            @$dosya_ismi = $_FILES['teklif_dosya']["name"];
            $benzersizsayi1=rand(10,1000);
            $isim1=$benzersizsayi1.$dosya_ismi;
            $isim=tum_bosluk_sil($isim1);
            $resim_yolu=substr($yuklemeklasoru, 3)."/".$isim;
            @move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim");   

            $dosyayukleme=$db->prepare("UPDATE teklif SET
              dosya_yolu=:dosya_yolu WHERE teklif_id=:teklif_id ");

            $yukleme=$dosyayukleme->execute(array(
              'dosya_yolu' => $resim_yolu,
              'teklif_id' => $_POST['teklif_id']
            ));

          };

          if ($guncelle) {
            header("location:../teklifler?durum=ok");
            exit;
          } else {
            header("location:../teklifler?durum=no");
            exit;
          }
          exit;
        }

//***********FİRMA************

        if (isset($_POST['firmaekle'])) {
          if (yetkikontrol()!="yetkili") {
            header("location:../index.php");
            exit;
          }

          $firmaekle=$db->prepare("INSERT INTO firma SET 
          	firma_isim=:isim,
          	firma_mail=:mail,
          	firma_tel=:tel
          	");

          $ekleme=$firmaekle->execute(array(
          	'isim' => guvenlik($_POST['firma_isim']),
            'mail' => guvenlik($_POST['firma_mail']),
            'tel' => guvenlik($_POST['firma_tel']),
             ));
if ($_FILES['firma_dosya']["error"]=="0") {
           $yuklemeklasoru = '../dosyalar';
           @$gecici_isim = $_FILES['firma_dosya']["tmp_name"];
           @$dosya_ismi = $_FILES['firma_dosya']["name"];
           $benzersizsayi1=rand(10,1000);
           $isim1=$benzersizsayi1.$dosya_ismi;
           $isim=tum_bosluk_sil($isim1);
           $resim_yolu=substr($yuklemeklasoru, 3)."/".$isim;
           move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim");

$son_eklenen_id=$db->lastInsertId();

           $dosyayukleme=$db->prepare("UPDATE firma SET
            dosya_yolu=:dosya_yolu WHERE firma_id=:firma_id ");

           $yukleme=$dosyayukleme->execute(array(
            'dosya_yolu' => $resim_yolu,
            'firma_id' => $son_eklenen_id
          ));
         }

   if ($ekleme) {
          header("location:../firmalar?durum=ok");
          exit;
        } else {
          header("location:../firmalar?durum=no");
          exit;
        }
        exit;
      }
if (isset($_POST['firmaguncelle'])) {
        if (yetkikontrol()!="yetkili") {
          header("location:../index.php");
          exit;
        }
$firmaguncelle=$db->prepare("UPDATE firma SET
          firma_isim=:isim,
          firma_mail=:mail,
          firma_tel=:tel
          WHERE firma_id={$_POST['firma_id']}");

		$guncelle=$firmaguncelle->execute(array(
          'isim' => guvenlik($_POST['firma_isim']),
          'mail' => guvenlik($_POST['firma_mail']),
          'tel' => guvenlik($_POST['firma_tel'])
            ));

        if ($_FILES['firma_dosya']['error']=="0") {

          $yuklemeklasoru = '../dosyalar';
          @$gecici_isim = $_FILES['firma_dosya']["tmp_name"];
          @$dosya_ismi = $_FILES['firma_dosya']["name"];
          $benzersizsayi1=rand(10,1000);
          $isim1=$benzersizsayi1.$dosya_ismi;
          $isim=tum_bosluk_sil($isim1);
          $resim_yolu=substr($yuklemeklasoru, 3)."/".$isim;
          @move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim");

          if ($_POST['dosya_sil']=="sil") {
            $dosya_yolu="";
          } else {
            $dosya_yolu=$resim_yolu;
          };

          $dosyayukleme=$db->prepare("UPDATE firma SET
            dosya_yolu=:dosya_yolu WHERE firma_id=:firma_id ");

          $yukleme=$dosyayukleme->execute(array(
            'dosya_yolu' => $dosya_yolu,
            'firma_id' => $_POST['firma_id']
          ));

        }
 if ($guncelle) {
          header("location:../firmalar?durum=ok");
          exit;
        } else {
          echo "\nPDOStatement::errorInfo():\n";
          $arr = $guncelle->errorInfo();
          print_r($arr);
          exit;
        }
        exit;
      }

 if (isset($_POST['sifreguncelle'])) {
        if (yetkikontrol()!="yetkili") {
          header("location:../index.php");
          exit;
        }
        $eskisifre=guvenlik($_POST['eskisifre']);
        $yenisifre_bir=guvenlik($_POST['yenisifre_bir']); 
        $yenisifre_iki=guvenlik($_POST['yenisifre_iki']);

        $kul_sifre=md5($eskisifre);

        $kullanicisor=$db->prepare("SELECT * FROM kullanicilar WHERE kul_sifre=:sifre AND kul_id=:id");
        $kullanicisor->execute(array(
          'id' => guvenlik($_POST['kul_id']),
          'sifre' => $kul_sifre
        ));

//dönen satır sayısını belirtir
        $say=$kullanicisor->rowCount();

        if ($say==0) {
          header("Location:../profil?durum=eskisifrehata");
        } else {
//eski şifre doğruysa başla
          if ($yenisifre_bir==$yenisifre_iki) {
           if (strlen($yenisifre_bir)>=6) {
//md5 fonksiyonu şifreyi md5 şifreli hale getirir.
            $sifre=md5($yenisifre_bir);
            $kullanici_yetki=0;
            $kullanicikaydet=$db->prepare("UPDATE kullanicilar SET
             kul_sifre=:kul_sifre
             WHERE kul_id=:kul_id");

            $insert=$kullanicikaydet->execute(array(
             'kul_sifre' => $sifre,
             'kul_id'=>guvenlik($_POST['kul_id'])
           ));

            if ($insert) {
             header("Location:../profil.php?durum=sifredegisti");
//Header("Location:../production/genel-ayarlar?durum=ok");
           } else {
             header("Location:../profil.php?durum=no");
           }

// Bitiş
         } else {
          header("Location:../profil.php?durum=eksiksifre");
        }

      } else {
       header("Location:../profil?durum=sifreleruyusmuyor");
       exit;
     }
   }
   exit;
   if ($update) {
    header("Location:../profil?durum=ok");

  } else {
    header("Location:../profil?durum=no");
  }
}


/********************************************************************************/


if (isset($_POST['profilguncelle'])) {
  if (yetkikontrol()!="yetkili") {
    header("location:../index.php");
    exit;
  }
  if (isset($_SESSION['kul_mail'])) {

			$boyut = $_FILES['kul_logo']['size'];//Dosya boyutumuzu alıp değişkene aktardık.
            if($boyut > 3145728)//Burada dosyamız 3 mb büyükse girmesini söyledik
            {
            //İsteyen arkadaslar burayı istediği gibi değiştirebilir size kalmış bir şey
                echo 'Dosya 3MB den büyük olamaz.';// 3 mb büyükse ekrana yazdıracağımız alan
              } else {
               $yuklemeklasoru = '../img';
               @$gecici_isim = $_FILES['kul_logo']["tmp_name"];
               @$dosya_ismi = $_FILES['kul_logo']["name"];
               $benzersizsayi1=rand(10000,99999);
               $benzersizsayi2=rand(10000,99999);
               $isim=$benzersizsayi1.$benzersizsayi2.$dosya_ismi;
               $resim_yolu=substr($yuklemeklasoru, 3)."/".tum_bosluk_sil($isim);
               @move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim");            	
             }

             $uzunluk=strlen($resim_yolu);
             if ($uzunluk<18) {
               $profilguncelle=$db->prepare("UPDATE kullanicilar SET
                kul_isim=:isim,
                kul_mail=:mail,
                kul_telefon=:telefon,
                kul_unvan=:unvan WHERE session_mail=:session_mail");
               $ekleme=$profilguncelle->execute(array(
                'isim' => guvenlik($_POST['kul_isim']),
                'mail' => guvenlik($_POST['kul_mail']),
                'telefon' => guvenlik($_POST['kul_telefon']),
                'unvan' => guvenlik($_POST['kul_unvan']),
                'session_mail' => $_SESSION['kul_mail']
              ));
   
               if ($ekleme) {
                header("Location:../profil?durum=ok");
              } else {

                header("Location:../profil?durum=no");
              }
              exit;
            } else {
            	$profilguncelle=$db->prepare("UPDATE kullanicilar SET
            		kul_isim=:isim,
            		kul_mail=:mail,
            		kul_telefon=:telefon,
            		kul_unvan=:unvan,
            		kul_logo=:logo WHERE session_mail=:session_mail");
            	$ekleme=$profilguncelle->execute(array(
            		'isim' => guvenlik($_POST['kul_isim']),
            		'mail' => guvenlik($_POST['kul_mail']),
            		'telefon' => guvenlik($_POST['kul_telefon']),
            		'unvan' => guvenlik($_POST['kul_unvan']),
            		'logo' => $resim_yolu,
            		'session_mail' => $_SESSION['kul_mail']
            	));

            	if ($ekleme) {
            		header("Location:../profil?durum=ok");
            	} else {
            		header("Location:../profil?durum=noff");
            	}
            	exit;
            }

          }
          header("Location:../profil");
          exit;

        }


        //***********firma silme

if (isset($_POST['firmasilme'])) {
          if (yetkikontrol()!="yetkili") {
            header("location:../index.php");
            exit;
          }
          $sil=$db->prepare("DELETE from firma where firma_id=:id");
          $kontrol=$sil->execute(array(
            'id' => guvenlik($_POST['firma_id'])
          ));

          if ($kontrol) {
     echo "kayıt başarılı";
            header("location:../firmalar?durum=ok");
            exit;
          } else {
      echo "kayıt başarısız";
            header("location:../firmalar?durum=no");
            exit;

          }
        }

if (isset($_POST['teklifsilme'])) {
          if (yetkikontrol()!="yetkili") {
            header("location:../index.php");
            exit;
          }
          $sil=$db->prepare("DELETE from teklif where teklif_id=:id");
          $kontrol=$sil->execute(array(
            'id' => guvenlik($_POST['teklif_id'])
          ));

          if ($kontrol) {
//echo "kayıt başarılı";
            header("location:../teklifler?durum=ok");
            exit;
          } else {
//echo "kayıt başarısız";
            header("location:../teklifler?durum=no");
            exit;

          }
        }

 ?>