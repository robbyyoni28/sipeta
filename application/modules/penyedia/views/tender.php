<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Partisipasi Tender</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalAddTender">
        <i class="fas fa-plus fa-sm text-white-50"></i> Input Tender Baru
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Kode Tender</th>
                    <th>Nama Tender</th>
                    <th>Tanggal Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tender as $t): ?>
                <tr>
                    <td><?= $t->kode_tender ?></td>
                    <td><?= $t->nama_tender ?></td>
                    <td><?= $t->tanggal_input ?></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAddTender" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('penyedia/tender_add') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Input Tender</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Tender</label>
                        <input type="text" name="kode_tender" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Tender</label>
                        <input type="text" name="nama_tender" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Personel yang digunakan</label>
                        <select name="personel_ids[]" class="form-control select2" multiple required>
                            <?php foreach($personel as $p): ?>
                                <option value="<?= $p->id ?>"><?= $p->nama ?> - <?= $p->jabatan ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih Peralatan yang digunakan</label>
                        <select name="peralatan_ids[]" class="form-control select2" multiple required>
                            <?php foreach($peralatan as $pl): ?>
                                <option value="<?= $pl->id ?>"><?= $pl->nama_alat ?> - <?= $pl->plat_serial ?></option>
                            <?php endforeach; ?>
                        </select>
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
