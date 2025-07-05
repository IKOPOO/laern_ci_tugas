<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-6">
    <!-- Vertical Form -->
    <?= form_open('buy', 'class="row g-3"') ?>
    <?= form_hidden('username', session()->get('username')) ?>
    <?= form_input(['type' => 'hidden', 'name' => 'total_harga', 'id' => 'total_harga', 'value' => '']) ?>
      <div class="col-12">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" value="<?php echo session()->get('username'); ?>">
      </div>
      <div class="col-12">
          <label for="alamat" class="form-label">Alamat</label>
          <input type="text" class="form-control" id="alamat" name="alamat">
      </div>
      <div class="col-12">
        <label for="kelurahan" class="form-label">Kelurahan</label>
        <select class="form-control" id="kelurahan" name="kelurahan" required></select>
      </div>
      <div class="col-12">
        <label for="layanan" class="form-label">Layanan</label>
        <select class="form-control" id="layanan" name="layanan" required></select>
      </div>
      <div class="col-12">
        <label for="ongkir" class="form-label">Ongkir</label>
        <input type="text" class="form-control" id="ongkir" name="ongkir" readonly>
      </div>
  </div>
    <div class="col-lg-6">
      <!-- Vertical Form -->
      <div class="col-12">
        <!-- Default Table -->
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Nama</th>
              <th scope="col">Harga</th>
              <th scope="col">Jumlah</th>
              <th scope="col">Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $diskon = session()->get('diskon') ?? 0;
              $totalDiskon = 0;
              $grandTotal = 0;

              if (!empty($items)) :
                foreach ($items as $index => $item) :
                  $hargaAsli = $item['price'];
                  $jumlah = $item['qty'];
                  $hargaSetelahDiskon = max(0, $hargaAsli - $diskon);
                  $subtotalDiskon = $hargaSetelahDiskon * $jumlah;
                  $totalDiskon += $diskon * $jumlah;
                  $grandTotal += $subtotalDiskon;
              ?>
            <tr>
              <td><?= $item['name'] ?></td>
              <td><?= number_to_currency($hargaAsli, 'IDR') ?></td>
              <td><?= $jumlah ?></td>
              <td><?= number_to_currency($diskon, 'IDR') ?></td>
              <td><?= number_to_currency($subtotalDiskon, 'IDR') ?></td>
            </tr>
            <?php
              endforeach;
              endif;  
            ?>
            <tr>
                <td colspan="4" class="text-end"><strong>Total Diskon</strong></td>
                <td><?= number_to_currency($totalDiskon, 'IDR') ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                <td><?= number_to_currency($grandTotal, 'IDR') ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>Total + Ongkir</strong></td>
                <td><span id="total"><?= number_to_currency($grandTotal, 'IDR') ?></span></td>
            </tr>
          </tbody>
        </table>
        <!-- End Default Table Example -->
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-primary">Buat Pesanan</button>
      </div>
    </form><!-- Vertical Form -->
  </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(document).ready(function() {
  var ongkir = 0;
  var total = 0;
  hitungTotal();

  $('#kelurahan').select2({
    placeholder: 'Ketik nama kelurahan...',
    ajax: {
      url: '<?= base_url('get-location') ?>',
      dataType: 'json',
      delay: 1500,
      data: function (params) {
        return {
          search: params.term
        };
      },
      processResults: function (data) {
        return {
          results: data.map(function(item) {
          return {
            id: item.id,
            text: item.subdistrict_name + ", " + item.district_name + ", " + item.city_name + ", " + item.province_name + ", " + item.zip_code
          };
          })
        };
      },
      cache: true
    },
    minimumInputLength: 3
  });


  $("#kelurahan").on('change', function() {
    var id_kelurahan = $(this).val();
    $("#layanan").empty();
    ongkir = 0;

    $.ajax({
      url: "<?= site_url('get-cost') ?>",
      type: 'GET',
      data: {
        'destination': id_kelurahan,
      },
      dataType: 'json',
      success: function(data) {
        data.forEach(function(item) {
          var text = item["description"] + " (" + item["service"] + ") : estimasi " + item["etd"] + "";
          $("#layanan").append($('<option>', {
            value: item["cost"],
            text: text
          }));
        });
        hitungTotal();
      },
    });
  });

  $("#layanan").on('change', function() {
    ongkir = parseInt($(this).val());
    hitungTotal();
  });
  function hitungTotal() {
    total = ongkir + <?= $grandTotal ?>;

    $("#ongkir").val(ongkir);
    $("#total").html("IDR " + total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,'));
    $("#total_harga").val(total);
  }
});
</script>
<?= $this->endSection() ?>
