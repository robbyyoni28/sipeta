<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Personel: <?= $company->nama_perusahaan ?></h1>
    <div>
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addPersonelModal">
            <i class="fas fa-plus fa-sm"></i> Tambah Personel
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
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jabatan</th>
                        <th>Jenis SKK</th>
                        <th>Nomor SKK</th>
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
                        <td><?= $p->jenis_skk ?></td>
                        <td><?= $p->nomor_skk ?></td>
                        <td><?= $p->masa_berlaku_skk ?></td>
                        <td>
                            <button class="btn btn-sm btn-info-light btn-pill btn-detail-resource" data-id="<?= $p->id ?>" data-type="personel_lapangan" title="Lihat Riwayat">
                                <i class="fas fa-history"></i> Detail
                            </button>
                            <button class="btn btn-sm btn-info btn-pill"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Personel -->
<div class="modal fade" id="addPersonelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Personel Ahli/Teknis</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url($module.'/personel_add/'.$company->id) ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Site Manager">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis SKK</label>
                                <input type="text" name="jenis_skk" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Nomor SKK</label>
                                <input type="text" name="nomor_skk" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Masa Berlaku SKK</label>
                                <input type="text" name="masa_berlaku_skk" class="form-control datepicker" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Upload SKK (PDF/Image)</label>
                                <input type="file" name="file_skk" class="form-control-file">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Surat Pernyataan</label>
                                <input type="file" name="file_surat_pernyataan" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Personel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Single Detail Modal -->
<div class="modal fade" id="resourceDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 90vh;">
        <div class="modal-content border-0 shadow-lg">
            <div id="modalHeader" class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Detail Informasi & Riwayat</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div id="resourceBasicInfo" class="row mb-4">
                    <!-- Loaded via JS -->
                </div>
                
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-history mr-2 text-primary"></i>Riwayat Tender / Penggunaan
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped small">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Tender</th>
                                <th>Nama Paket / Tender</th>
                                <th>Tahun</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-detail-resource').on('click', function() {
        let btn = $(this);
        let id = btn.data('id');
        let type = btn.data('type');
        let row = btn.closest('tr');
        
        let nama = row.find('td:nth-child(1)').text();
        let nik = row.find('td:nth-child(2)').text();
        let jabatan = row.find('td:nth-child(3)').text();
        
        $('#resourceBasicInfo').html(`
            <div class="col-md-6 mb-2">
                <small class="text-muted font-weight-bold d-block">Nama Lengkap</small>
                <div class="h6 font-weight-bold text-dark">${nama}</div>
            </div>
            <div class="col-md-6 mb-2">
                <small class="text-muted font-weight-bold d-block">NIK</small>
                <div class="h6"><span class="badge badge-light border px-2 py-1">${nik}</span></div>
            </div>
            <div class="col-md-12 mb-2">
                <small class="text-muted font-weight-bold d-block">Jabatan</small>
                <div class="h6 text-primary font-weight-bold">${jabatan}</div>
            </div>
        `);
        
        $('#historyTableBody').html('<tr><td colspan="4" class="text-center py-3"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...</td></tr>');
        $('#resourceDetailModal').modal('show');
        
        $.ajax({
            url: '<?= base_url($module."/get_resource_history") ?>',
            method: 'GET',
            data: { id: id, type: type },
            dataType: 'JSON',
            success: function(res) {
                let rows = '';
                if (res && res.length > 0) {
                    res.forEach((h, i) => {
                        rows += `
                            <tr>
                                <td class="text-center">${i+1}</td>
                                <td><code class="text-primary font-weight-bold">${h.kode_tender}</code></td>
                                <td>
                                    <div class="font-weight-bold text-dark">${h.nama_tender}</div>
                                    <div class="small text-muted">${h.judul_paket || '-'}</div>
                                </td>
                                <td class="text-center"><span class="badge badge-dark">${h.tahun_anggaran}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center py-3 text-muted">Belum ada riwayat tender.</td></tr>';
                }
                $('#historyTableBody').html(rows);
            }
        });
    });
});
</script>
