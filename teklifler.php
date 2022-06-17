<?php 
include'header.php' 
?>

<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<style type="text/css" media="screen">
  @media only screen and (max-width: 700px) {
    .mobilgizle {
      display: none;
    }
    .mobilgizleexport {
      display: none;
    }
    .mobilgoster {
      display: block;
    }
  }
  @media only screen and (min-width: 700px) {
    .mobilgizleexport {
      display: flex;
    }
    .mobilgizle {
      display: block;
    }
    .mobilgoster {
      display: none;
    }
  }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
  <h1 class="h3 mb-2 text-gray-800">Teklifler</h1>
  <p class="mb-4">Burada alandan tekliflere ait bilgileri görüntüleyebilir ve dışa aktarabilirsiniz.</p>
  <!-- DataTales Giriş -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Teklifler</h6>
    </div>
    <div class="card-body" style="width: 100%">
      <button type="button"class="btn btn-sm btn-info btn-icon-split mobilgoster">
        <span class="icon text-white-65">
          <i class="fas fa-edit"></i>
        </span>
        <span class="text">Seçenekler</span>
      </button>

      <div class="mobilgizle gizlemeyiac" style="margin-bottom: 10px;">
        <!--Tablo filtreleme butonları bölümü giriş-->
        <button type="button" id="hepsi" style="margin-bottom: 5px;" class="btn btn-sm btn-info btn-icon-split">
          <span class="icon text-white-65">
            <i class="fas fa-edit"></i>
          </span>
          <span class="text">Hepsi</span>
        </button>

        <button type="button" id="yeni" style="margin-bottom: 5px;" class="btn btn-sm btn-dark btn-icon-split">
          <span class="icon text-white-65">
            <i class="fas fa-hourglass-start"></i>
          </span>
          <span class="text">Yeni Başlananlar</span>
        </button>

        <button type="button" id="devam" style="margin-bottom: 5px;" class="btn btn-sm btn-info btn-icon-split">
          <span class="icon text-white-65">
            <i class="fas fa-sync-alt"></i>
          </span>
          <span class="text">Devam Edenler</span>
        </button>

        <button type="button" id="bitti" style="margin-bottom: 5px;" class="btn btn-sm btn-success btn-icon-split">
          <span class="icon text-white-65">
            <i class="fas fa-check"></i>
          </span>
          <span class="text">Bitenler</span>
        </button>
        <span class="dropdown no-arrow">
         <button data-toggle="dropdown" aria-expanded="false" type="button" id="aktarmagizleme" style="margin-left: 4px; margin-bottom: 5px;" class="btn btn-sm btn-primary btn-icon-split dropdown-toggle"><span class="icon text-white-65"><i class="fas fa-file-export"></i></span><span class="text">Dışa Aktar</span>
         </button> 
         <div class="dropdown-menu" aria-labelledby="aktarmagizleme">
          <a class="dropdown-item" href="#"> 
            <button type="button" onclick="fnAction('copy');" title="asdsad" attr="Tabloyu Kopyala" class="btn btn-sm btn-icon-split btn-dark">
              <span class="icon text-white-65">
                <i class="fas fa-copy"></i>
              </span>
              <span class="text">Kopyala</span>
            </button>
          </a>
          <a class="dropdown-item" title="">  
            <button type="button" onclick="fnAction('excel');" attr="Excel Formatında Dışa Aktar" class="btn btn-sm btn-icon-split btn-success">
              <span class="icon text-white-65">
                <i class="fas fa-file-excel"></i>
              </span>
              <span class="text">Excel</span>
            </button>
          </a>
          <a class="dropdown-item" href="#">
            <button type="button" onclick="fnAction('pdf');" attr="PDF Formatında Dışa Aktar" class="btn btn-sm btn-icon-split btn-danger">
              <span class="icon text-white-65">
                <i class="fas fa-file-pdf"></i>
              </span>
              <span class="text">PDF</span>
            </button>
          </a>
          <a class="dropdown-item" href="#">
            <button type="button" onclick="fnAction('csv');" attr="CSV Formatında Dışa Aktar" class="btn btn-sm btn-icon-split btn-primary">
              <span class="icon text-white-65">
                <i class="fas fa-file-csv"></i>
              </span>
              <span class="text">CSV</span>
            </button>
          </a>
        </div>
      </span>
      </div>
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr> 
            <th>No</th>
            <th>Başlık</th>
            <th>Bitiş Tarihi</th>
            <th>Onay Durumu</th>
            <th>Durum</th>
            <th>İşlem</th>
          </tr>
        </thead>
        <tbody>
         <?php 
         $say=0;
         $teklifsor=$db->prepare("SELECT * FROM teklif ORDER BY teklif_id DESC");
         $teklifsor->execute();
         while ($teklifcek=$teklifsor->fetch(PDO::FETCH_ASSOC)) { $say++?>
          <tr>
            <td><?php echo $say; ?></td>
            <td><?php echo $teklifcek['teklif_baslik']; ?></td>
            <td><?php echo $teklifcek['teklif_teslim_tarihi']; ?></td>
            <td><?php 
            if ($teklifcek['teklif_onay']=="Onaylı") {
              echo '<span class="badge badge-danger" style="font-size:1rem">Onaylı</span>';
            } else {
              echo $teklifcek['teklif_onay'];
            }
            ?></td>
            <td><?php 
            if ($teklifcek['teklif_durum']=="Bitti") {
              echo '<span class="badge badge-success" style="font-size:1rem">Bitti</span>';
           } else {
            echo $teklifcek['teklif_durum'];
          }
          ?></td>
          <td>
            <?php 
            if (yetkikontrol()=="yetkili") {?>
             <div class="d-flex justify-content-center">
              <form action="teklifduzenle.php" method="POST">
                <input type="hidden" name="teklif_id" value="<?php echo $teklifcek['teklif_id'] ?>">
                <button type="submit" name="duzenleme" class="btn btn-success btn-sm btn-icon-split">
                  <span class="icon text-white-60">
                    <i class="fas fa-edit"></i>
                  </span>
                </button>
              </form>
              <form class="mx-1" action="islemler/islem.php" method="POST">
                <input type="hidden" name="teklif_id" value="<?php echo $teklifcek['teklif_id'] ?>">
                <button type="submit" name="teklifsilme" class="btn btn-danger btn-sm btn-icon-split">
                  <span class="icon text-white-60">
                    <i class="fas fa-trash"></i>
                  </span>
                </button>
              </form>
            <?php } ?>
            <form action="teklif" method="POST">
              <input type="hidden" name="teklif_id" value="<?php echo $teklifcek['teklif_id'] ?>">
              <button type="submit" name="teklif_bak" class="btn btn-primary btn-sm btn-icon-split">
                <span class="icon text-white-60">
                  <i class="fas fa-eye"></i>
                </span>
              </button>
            </form>
          </div>
        </td>
      </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <tr> 
      <th>No</th>
      <th>Başlık</th>
      <th>Bitiş Tarihi</th>
      <th>Aciliyet</th>
      <th>Durum</th>
      <th>İşlem</th>
    </tr>
  </tfoot>
 
</table>
</div>
</div>
</div>
</div>
<?php include'footer.php' ?>

<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/demo/datatables-demo.js"></script> 
<script src="vendor/datatables/dataTables.buttons.min.js"></script>
<script src="vendor/datatables/buttons.flash.min.js"></script>
<script src="vendor/datatables/jszip.min.js"></script>
<script src="vendor/datatables/pdfmake.min.js"></script>
<script src="vendor/datatables/vfs_fonts.js"></script>
<script src="vendor/datatables/buttons.html5.min.js"></script>
<script src="vendor/datatables/buttons.print.min.js"></script>

<script type="text/javascript">
  $("#aktarmagizleme").click(function(){
    $(".dt-buttons").toggle();
  });
</script>
<script type="text/javascript">
  $(".mobilgoster").click(function(){
    $(".gizlemeyiac").toggle();
  });
</script>
<script>
  var dataTables = $('#dataTable').DataTable({
    initComplete: function () {
      this.api().columns([2,3,4]).every( function () {
        var column = this;
        var select = $('<select class="filtre"><option value=""></option></select>')
        .appendTo( $(column.footer()).empty() )
        .on( 'change', function () {
          var val = $.fn.dataTable.util.escapeRegex(
            $(this).val()
            );

          column
          .search( val ? '^'+val+'$' : '', true, false )
          .draw();
        });

        column.data().unique().sort().each( function ( d, j ) {
         var val = $('<div/>').html(d).text();
          
          if (val.length>29) {
            filtremetin =  val.substr(0,30)+"...";
          } else {
            filtremetin=val;
          }
          select.append( '<option value="' + val + '">' + filtremetin + '</option>' )
        });
      });
    },
    "ordering": true,  
    "searching": true,  
    "lengthChange": true, 
    "info": true,
    dom: "<'row mobilgizleexport gizlemeyiac'<'col-md-6'l><'col-md-6'f><'col-md-4 d-none d-print-block'B>>rtip",
    buttons: [
    {
      extend: 'copyHtml5', 
      className: 'kopyalama-buton',
      messageBottom: "MDS",
    },
    {
      extend: 'excelHtml5', 
      className: 'excel-buton',
      messageBottom: "MDS",
    },
    {
     extend: 'pdfHtml5',
     className: 'pdf-buton',
     messageBottom: "MDS",
   },
   {
    extend: 'csvHtml5',
    className: 'csv-buton',
    messageBottom: "MDS",
  }
  ]
});
  function fnAction(action) {
    switch (action) {
      case "excel":
      $('.excel-buton').trigger('click');
      break;
      case "pdf":
      $('.pdf-buton').trigger('click');
      break;
      case "copy":
      $('.kopyalama-buton').trigger('click');
      break;
      case "csv":
      $('.csv-buton').trigger('click');
      break;
    }
  }
 //Tablo filtreleme 
$('#hepsi').on('click', function () {
    dataTables
    .columns()
    .search( '' )
    .columns( '.sold_out' )
    .search( 'YES' )
    .draw();
    dataTables.column(3).search("").draw();
  }); 
$('#bitti').on('click', function () {
    dataTables
    .columns()
    .search( '' )
    .columns( '.sold_out' )
    .search( 'YES' )
    .draw();
    dataTables.column(4).search("Bitti").draw();
  }); 
$('#devam').on('click', function () {
    dataTables
    .columns()
    .search( '' )
    .columns( '.sold_out' )
    .search( 'YES' )
    .draw();
    dataTables.column(4).search("Devam Ediyor").draw();
  }); 
  $('#yeni').on('click', function () {
    dataTables
    .columns()
    .search( '' )
    .columns( '.sold_out' )
    .search( 'YES' )
    .draw();
    dataTables.column(4).search("Yeni Başladı").draw();
  });
</script>
<?php if (@$_GET['durum']=="no")  {?>  
  <script>
    Swal.fire({
      type: 'error',
      title: 'İşlem Başarısız',
      text: 'Lütfen Tekrar Deneyin',
      showConfirmButton: true,
      confirmButtonText: 'Kapat'
    })
  </script>
<?php } ?>

<?php if (@$_GET['durum']=="ok")  {?>  
  <script>
    Swal.fire({
      type: 'success',
      title: 'İşlem Başarılı',
      text: 'İşleminiz Başarıyla Gerçekleştirildi',
      showConfirmButton: false,
      timer: 2000
    })
  </script>
<?php } ?>

<?php if (@$_GET['durum']=="hata")  {?>  
  <script>
    Swal.fire({
      type: 'error',
      title: 'İşlem Başarısız',
      text: 'Yapmamanız Gereken Şeyler Yapıyorsunuz!',
      showConfirmButton: false,
      timer: 3000
    })
  </script>
  <?php } ?>