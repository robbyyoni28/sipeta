<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Peralatan: <?= $company->nama_perusahaan ?></h1>
    <div>
        <button class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#addPeralatanModal">
            <i class="fas fa-plus fa-sm"></i> Tambah Peralatan
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
                        <th>Nama Alat</th>
                        <th>Merk / Tipe</th>
                        <th>Kapasitas</th>
                        <th>No. Seri / Plat</th>
                        <th>Kepemilikan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($peralatan as $pl): ?>
                    <tr>
                        <td><strong><?= $pl->nama_alat ?></strong></td>
                        <td><?= $pl->merk ?> / <?= $pl->tipe ?></td>
                        <td><?= $pl->kapasitas ?></td>
                        <td><?= $pl->plat_serial ?></td>
                        <td><?= $pl->bukti_kepemilikan ?></td>
                        <td>
                            <button class="btn btn-sm btn-success-light btn-pill btn-detail-resource" data-id="<?= $pl->id ?>" data-type="peralatan" title="Lihat Riwayat">
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

<!-- Modal Tambah Peralatan -->
<div class="modal fade" id="addPeralatanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrasi Alat Berat / Peralatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url($module.'/peralatan_add/'.$company->id) ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Alat</label>
                                <input type="text" name="nama_alat" class="form-control" required placeholder="Contoh: Bulldozer D65">
                            </div>
                            <div class="form-group">
                                <label>Merk</label>
                                <input type="text" name="merk" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Tipe</label>
                                <input type="text" name="tipe" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kapasitas</label>
                                <input type="text" name="kapasitas" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Nomor Seri / Plat</label>
                                <input type="text" name="plat_serial" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Bukti Kepemilikan</label>
                                <input type="text" name="bukti_kepemilikan" class="form-control" placeholder="Faktur / Invoice">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Upload Bukti (PDF/Image)</label>
                                <input type="file" name="file_bukti" class="form-control-file">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Dokumentasi Alat</label>
                                <input type="file" name="file_dokumentasi" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Peralatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Single Detail Modal -->
<div class="modal fade" id="resourceDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 90vh;">
        <div class="modal-content border-0 shadow-lg">
            <div id="modalHeader" class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-tools mr-2"></i>Detail Peralatan & Riwayat</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div id="resourceBasicInfo" class="row mb-4">
                    <!-- Loaded via JS -->
                </div>
                
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-history mr-2 text-success"></i>Riwayat Tender / Penggunaan
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
        let merk = row.find('td:nth-child(2)').text();
        let plat = row.find('td:nth-child(4)').text();
        
        $('#resourceBasicInfo').html(`
            <div class="col-md-6 mb-2">
                <small class="text-muted font-weight-bold d-block">Nama Alat</small>
                <div class="h6 font-weight-bold text-dark">${nama}</div>
            </div>
            <div class="col-md-6 mb-2">
                <small class="text-muted font-weight-bold d-block">Merk / Tipe</small>
                <div class="h6 font-weight-bold text-dark">${merk}</div>
            </div>
            <div class="col-md-6 mb-2">
                <small class="text-muted font-weight-bold d-block">No. Seri / Plat</small>
                <div class="h6"><code class="bg-light px-2 py-1">${plat}</code></div>
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
