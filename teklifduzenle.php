<?php 
include 'header.php' ;

if (yetkikontrol()!="yetkili") {
	header("location:index.php?durum=izinsiz");
	exit;
}
if (isset($_POST['teklif_id'])) {
	$teklifsor=$db->prepare("SELECT * FROM teklif where teklif_id=:id");
	$teklifsor->execute(array(
		'id' => guvenlik($_POST['teklif_id'])
	));
	$teklifcek=$teklifsor->fetch(PDO::FETCH_ASSOC);
} else {
	header("location:teklifler");
} 
?>
<?php
$teklifindetaymetni=$teklifcek['teklif_detay'];
$dosyayolu=$teklifcek['dosya_yolu']
?>
<link rel="stylesheet" media="all" type="text/css" href="vendor/upload/css/fileinput.min.css">
<link rel="stylesheet" type="text/css" media="all" href="vendor/upload/themes/explorer-fas/theme.min.css">
<script src="vendor/upload/js/fileinput.js" type="text/javascript" charset="utf-8"></script>
<script src="vendor/upload/themes/fas/theme.min.js" type="text/javascript" charset="utf-8"></script>
<script src="vendor/upload/themes/explorer-fas/theme.minn.js" type="text/javascript" charset="utf-8"></script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h5 class="m-0 font-weight-bold text-primary">Teklif Düzenleme  
						<small>
							<?php 
							if (isset($_GET['islem'])) { 
								if ($_GET['islem']=="ok") {?> 
									<b style="color: green; font-size: 16px;">İşlem Başarılı</b>
								<?php } elseif ($_GET['islem']=="no") { ?> 
									<b style="color: red; font-size: 16px;">İşlem Başarısız</b>
								<?php } } ?>

								</small>
						</h5>
					</div>
					<div class="card-body">
						<form action="islemler/islem.php" method="POST" enctype="multipart/form-data"  data-parsley-validate>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label>Teklif Başlık</label>
									<input required type="text" class="form-control" name="teklif_baslik" value="<?php echo $teklifcek['teklif_baslik'] ?>">
								</div>
								<div class="form-group col-md-6">
									<label>Bitirme Tarihi</label>
									<input required type="date" class="form-control" name="teklif_teslim_tarihi" value="<?php echo $teklifcek['teklif_teslim_tarihi'] ?>">
									</div>
							</div>

							<div class="form-row">
								<?php $teklifonay=$teklifek['teklif_onay']; ?>
								<div required class="form-group col-md-6">
									<label>Onay Durumu</label>
									<select id="inputState" name="teklif_onay" class="form-control">
										<option <?php if($teklifonay == 'Onaysız'){echo("selected");}?> value="Onaysız">Müşteri Onaysız</option>
										<option <?php if($teklifonay == 'Onaylı'){echo("selected");}?> value="Onaylı">Müşteri Onaylı</option>
									</select>
								</div>
								<?php $durum=$teklifcek['teklif_durum']; ?>
								<div required class="form-group col-md-6">
									<label>Teklif Durumu</label>
									<select name="teklif_durum" class="form-control">
										<option <?php if($durum == 'Yeni Başladı'){echo("selected");}?> value="Yeni Başladı">Yeni Başladı</option>
										<option <?php if($durum == 'Devam Ediyor'){echo("selected");}?> value="Devam Ediyor">Devam Ediyor</option>
										<option <?php if($durum == 'Bitti'){echo("selected");}?> value="Bitti">Bitti</option>
									</select>
								</div>
							</div>
							<div class="form-row justify-content-center">	
								<div class="col-md-6">
									<div class="file-loading">
										<input type="file" class="form-control" id="teklifdosya" name="teklif_dosya" >
									</div>
								</div>
							</div>					
							<div class="form-row">
								<div class="form-group col-md-12">
									<textarea class="ckeditor" name="proje_detay" id="editor"><?php echo $teklifindetaymetni; ?></textarea>
								</div>
							</div>
							<input type="hidden" class="form-control" name="teklif_id" value="<?php echo $_POST['teklif_id'] ?>">
							<button style="width: fit-content;" type="submit" name="teklifguncelle" class="btn btn-success">Kaydet</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'footer.php' ?>
<script src="ckeditor/ckeditor.js"></script>
<script>
	CKEDITOR.replace( 'editor' );
</script>
<?php 
if (strlen($dosyayolu)>10) {?>
	<script>
		$(document).ready(function () {
			var url1='<?php echo $dosyayolu ?>'
			$("#teklifdosya").fileinput({
				'theme': 'explorer-fas',
				'showUpload': false,
				'showCaption': true,
				'showDownload': true,
			//	'initialPreviewAsData': true,
			allowedFileExtensions: ["jpg", "png", "jpeg", "mp4", "zip", "rar"],
			initialPreview: [
			'<img src="<?php echo $dosyayolu ?>" style="height:100px" class="file-preview-image" alt="Dosya" title="Dosya">'
			],
			initialPreviewConfig: [
			{downloadUrl: url1,
				showRemove: false,
			},
			],
		});

		});
	</script>
<?php } else { ?>
	<script>
		$(document).ready(function () {
			$("#teklifdosya").fileinput({
				'theme': 'explorer-fas',
				'showUpload': false,
				'showCaption': true,
				'showDownload': true,
			//	'initialPreviewAsData': true,
			allowedFileExtensions: ["jpg", "png", "jpeg", "mp4", "zip", "rar"],
		});

		});
	</script>
	<?php } ?>
