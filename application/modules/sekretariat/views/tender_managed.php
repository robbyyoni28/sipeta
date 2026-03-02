<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Tender: <?= $company->nama_perusahaan ?></h1>
    <div>
        <button class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#addTenderModal">
            <i class="fas fa-plus fa-sm"></i> Tambah Partisipasi Tender
        </button>
        <a href="<?= base_url($module.'/manage/'.$company->id) ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Kode Tender</th>
                        <th>Nama Tender</th>
                        <th>Tahun</th>
                        <th>Operator Input</th>
                        <th>Tanggal Input</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tender as $t): ?>
                    <tr>
                        <td><strong><?= $t->kode_tender ?></strong></td>
                        <td><?= $t->nama_tender ?></td>
                        <td><span class="badge badge-warning"><?= isset($t->tahun_anggaran) ? $t->tahun_anggaran : '-' ?></span></td>
                        <td><small><?= isset($t->created_by) ? $t->created_by : '-' ?></small></td>
                        <td><?= $t->tanggal_input ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary">Detail Aset</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Tender -->
<div class="modal fade" id="addTenderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Registrasi Partisipasi Tender</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url($module.'/tender_add/'.$company->id) ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Informasi Tender</label>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="kode_tender" class="form-control" required placeholder="Kode Tender (e.g. TDR-2026-001)">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="tahun_anggaran" class="form-control" required placeholder="Tahun" value="<?= date('Y') ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="nama_tender" class="form-control" required placeholder="Nama Paket Pekerjaan">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-weight-bold">Pilih Personel yang Ditugaskan</label>
                        <div class="p-3 bg-light rounded border">
                            <select name="personel_ids[]" class="form-control select2" multiple required>
                                <?php foreach($personel as $p): ?>
                                    <option value="<?= $p->id ?>"><?= $p->nama ?> - <?= $p->jabatan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-weight-bold">Pilih Peralatan yang Digunakan</label>
                        <div class="p-3 bg-light rounded border">
                            <select name="peralatan_ids[]" class="form-control select2" multiple required>
                                <?php foreach($peralatan as $pl): ?>
                                    <option value="<?= $pl->id ?>"><?= $pl->nama_alat ?> (<?= $pl->plat_serial ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info px-4">Daftarkan Tender</button>
                </div>
            </form>
        </div>
    </div>
</div>
