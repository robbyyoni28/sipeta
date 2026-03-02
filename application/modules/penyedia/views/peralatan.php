<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Peralatan</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalAddPeralatan">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Peralatan
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama Alat</th>
                    <th>Merk/Tipe</th>
                    <th>Kapasitas</th>
                    <th>Plat/Serial</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($peralatan as $p): ?>
                <tr>
                    <td><?= $p->nama_alat ?></td>
                    <td><?= $p->merk ?> / <?= $p->tipe ?></td>
                    <td><?= $p->kapasitas ?></td>
                    <td><?= $p->plat_serial ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAddPeralatan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('penyedia/peralatan_add') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Peralatan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Alat</label>
                            <input type="text" name="nama_alat" class="form-control" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Merk</label>
                            <input type="text" name="merk" class="form-control" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Tipe</label>
                            <input type="text" name="tipe" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kapasitas</label>
                            <input type="text" name="kapasitas" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Plat / Nomor Serial</label>
                            <input type="text" name="plat_serial" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Bukti Kepemilikan</label>
                            <input type="text" name="bukti_kepemilikan" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Upload Bukti (PDF/JPG)</label>
                            <input type="file" name="file_bukti" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Upload Dokumentasi (JPG)</label>
                            <input type="file" name="file_dokumentasi" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
