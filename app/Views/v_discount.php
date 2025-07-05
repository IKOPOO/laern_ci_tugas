<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashData('success')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashData('failed')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('failed') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    Tambah Diskon
</button>

<!-- Table with stripped rows -->
<table class="table datatable mt-3">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Nominal (Rp)</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($discount as $index => $row): ?>
            <tr>
                <th scope="row"><?= $index + 1 ?></th>
                <td><?= $row['tanggal'] ?></td>
                <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                <td>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['id'] ?>">
                      Ubah
                    </button>
                    <a href="<?= base_url('discount/delete/' . $row['id']) ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus data ini ?')">
                      Hapus
                    </a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Edit Diskon</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="<?= base_url('discount/edit/' . $row['id']) ?>" method="post">
                      <?= csrf_field(); ?>
                      <div class="modal-body">
                          <div class="form-group">
                              <label for="tanggal">Tanggal</label>
                              <input type="date" name="tanggal" class="form-control" required readonly onfocus="this.blur()" value="<?= $row['tanggal'] ?>" required>
                          </div>
                          <div class="form-group">
                              <label for="nominal">Nominal</label>
                              <input type="number" name="nominal" class="form-control" value="<?= $row['nominal'] ?>" required>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- End Edit Modal -->

        <?php endforeach; ?>
    </tbody>
</table>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title">Tambah Diskon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('discount/create') ?>" method="post">
          <?= csrf_field(); ?>
          <div class="modal-body">
              <div class="form-group">
                  <label for="tanggal">Tanggal</label>
                  <input type="date" name="tanggal" class="form-control" required>
              </div>
              <div class="form-group">
                  <label for="nominal">Nominal</label>
                  <input type="number" name="nominal" class="form-control" required>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- End Add Modal -->

<?= $this->endSection() ?>
