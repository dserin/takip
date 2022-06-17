<?php 
include 'header.php';
if (yetkikontrol()!="yetkili") {
  header("location:index.php?durum=izinsiz");
  exit;
}
?>
<link rel="stylesheet" media="all" type="text/css" href="vendor/upload/css/fileinput.min.css">
<link rel="stylesheet" type="text/css" media="all" href="vendor/upload/themes/explorer-fas/theme.min.css">
<script src="vendor/upload/js/fileinput.js" type="text/javascript" charset="utf-8"></script>
<script src="vendor/upload/themes/fas/theme.min.js" type="text/javascript" charset="utf-8"></script>
<script src="vendor/upload/themes/explorer-fas/theme.minn.js" type="text/javascript" charset="utf-8"></script>
<!-- Begin Page Content -->
<div class="container">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h5 class="m-0 font-weight-bold text-primary">Teklif Ekle</h5>
    </div>
    <div class="card-body">
      <form action="islemler/islem.php" method="POST" enctype="multipart/form-data"  data-parsley-validate>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Teklif Başlık</label>
            <input type="text" class="form-control" name="teklif_baslik" placeholder="Teklifin Başlığı">
          </div>
          <div class="form-group col-md-6">
            <label>Bitirme Tarihi</label>
            <input type="date" class="form-control" name="teklif_teslim_tarihi" placeholder="Teklifin Bitiş Gereken Tarih">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Teklif Durumu</label>
            <select name="teklif_durum" class="form-control">
              <option>Yeni Başladı</option>
              <option>Devam Ediyor</option>
              <option>Bitti</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="inputState">Onay Durumu</label>
            <select required name="teklif_onay" class="form-control">
              <option>Müşteri Onaysız</option>
              <option>Müşteri Onay verdi</option>
              </select>
          </div>
        </div>

        <div class="form-row justify-content-center">
        <div class="container">
          <label>Talep Form</label>
          <div class="row">
            <div class="col-md-4">

          <div class="">
            <input class="form-control"  name="teklif_dosya" type="file">
          </div>
        </div>
        <div class="col-md-4">

          <div class="">
            <input class="form-control" name="teklif_dosya" type="file">
          </div>
        </div>
        <div class="col-md-4">

          <div class="">
            <input class="form-control"  name="teklif_dosya" type="file">
          </div>
        </div>
            
          </div>
        </div>

         
      </div>
      <div class="form-row mt-2">
        <div class="form-group col-md-12">
          <textarea class="ckeditor" name="teklif_detay" id="editor"></textarea>
        </div>
      </div>
      <button type="submit" name="teklifekle" class="btn btn-primary">Kaydet</button>
    </form>
  </div>
</div>
</div>
<?php include 'footer.php' ?>
<script src="ckeditor/ckeditor.js"></script>
<script>
 CKEDITOR.replace('editor',{
 });
</script>
<script>
  $(document).ready(function () {
    var url1='<?php echo $ayarcek['site_logo'] ?>';
    $("#teklif_dosya").fileinput({
      'theme': 'explorer-fas',
      'showUpload': false,
      'showCaption': true,
      showDownload: true,
      allowedFileExtensions: ["jpg", "png", "jpeg","mp4","zip","rar"],
    });
  });
</script>