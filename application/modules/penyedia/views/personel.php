<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Personel</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalAddPersonel">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Personel
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Jabatan</th>
                    <th>SKK</th>
                    <th>Masa Berlaku</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($personel as $p): ?>
                <tr>
                    <td><?= $p->nama ?></td>
                    <td><?= $p->nik ?></td>
                    <td><?= $p->jabatan ?></td>
                    <td><?= $p->nomor_skk ?> (<?= $p->jenis_skk ?>)</td>
                    <td><?= $p->masa_berlaku_skk ?></td>
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
<div class="modal fade" id="modalAddPersonel" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('penyedia/personel_add') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Personel</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NIK</label>
                            <input type="text" name="nik" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jenis SKK</label>
                            <input type="text" name="jenis_skk" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor SKK</label>
                            <input type="text" name="nomor_skk" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Masa Berlaku SKK</label>
                            <input type="text" name="masa_berlaku_skk" class="form-control datepicker" placeholder="dd/mm/yyyy" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Upload SKK (PDF/JPG)</label>
                            <input type="file" name="file_skk" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Upload Surat Pernyataan (PDF/JPG)</label>
                            <input type="file" name="file_surat_pernyataan" class="form-control">
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
