<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-tie mr-2"></i>Data Manajer Keuangan</h1>
    <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalAddManajerK">
        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Manajer Keuangan
    </button>
</div>

<!-- Filter Card -->
<div class="card shadow mb-4 border-0" style="border-radius: 16px;">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="small font-weight-bold text-uppercase text-muted">Filter Perusahaan</label>
                <select id="filterPenyedia" class="form-control" style="border-radius: 10px;">
                    <option value="">-- Semua Perusahaan --</option>
                    <?php foreach($penyedia_list as $p): ?>
                    <option value="<?= $p->id ?>" <?= ($selected_penyedia == $p->id) ? 'selected' : '' ?>>
                        <?= $p->nama_perusahaan ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow mb-4 border-0" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f4c75 100%); border-radius: 16px 16px 0 0;">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-user-tie mr-2"></i>Daftar Manajer Keuangan</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tableManajerKeuangan" width="100%">
                <thead style="background: #f0f4f8;">
                    <tr>
                        <th class="px-4 py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b; letter-spacing:1px;">#</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Nama</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">NIK</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Jenis SKK</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Nomor SKK</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Masa Berlaku</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Perusahaan</th>
                        <th class="py-3" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Dokumen</th>
                        <th class="py-3 px-4" style="font-size:0.72rem; text-transform:uppercase; color:#64748b;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($manajer_list)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 d-block" style="color: #e2e8f0;"></i>
                            Belum ada data Manajer Keuangan.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($manajer_list as $i => $mk): ?>
                    <tr>
                        <td class="px-4 py-3"><?= $i + 1 ?></td>
                        <td class="py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm mr-3 d-flex align-items-center justify-content-center rounded-circle" style="width:36px;height:36px;background:linear-gradient(135deg,#065f46,#10b981);color:white;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                                    <?= strtoupper(substr($mk->nama, 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size:0.9rem;"><?= htmlspecialchars($mk->nama) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3"><code style="background:#f0f4f8;padding:3px 8px;border-radius:6px;"><?= $mk->nik ?? '-' ?></code></td>
                        <td class="py-3"><span class="badge badge-success" style="border-radius:6px;"><?= $mk->jenis_skk ?? '-' ?></span></td>
                        <td class="py-3"><?= $mk->nomor_skk ?? '-' ?></td>
                        <td class="py-3">
                            <?php 
                            $masa = $mk->masa_berlaku_skk ?? null;
                            if ($masa && $masa != '0000-00-00') {
                                $ts = strtotime($masa);
                                $kelas = ($ts < time()) ? 'badge-danger' : (($ts < strtotime('+6 months')) ? 'badge-warning' : 'badge-success');
                                echo "<span class='badge $kelas' style='border-radius:6px;'>".date('d/m/Y', $ts)."</span>";
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            ?>
                        </td>
                        <td class="py-3"><span class="badge badge-light border" style="border-radius:6px;"><?= htmlspecialchars($mk->nama_perusahaan ?? '-') ?></span></td>
                        <td class="py-3">
                            <?php if (!empty($mk->file_ktp)): ?>
                                <a href="<?= base_url('uploads/dokumen/'.$mk->file_ktp) ?>" target="_blank" class="badge badge-info py-1 px-2 mr-1"><i class="fas fa-id-card"></i> KTP</a>
                            <?php endif; ?>
                            <?php if (!empty($mk->file_skk)): ?>
                                <a href="<?= base_url('uploads/dokumen/'.$mk->file_skk) ?>" target="_blank" class="badge badge-success py-1 px-2"><i class="fas fa-certificate"></i> SKK</a>
                            <?php endif; ?>
                            <?php if (empty($mk->file_ktp) && empty($mk->file_skk)): ?>
                                <span class="text-muted small">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <button class="btn btn-sm btn-outline-warning btn-edit-manajer-k" 
                                data-id="<?= $mk->id ?>"
                                data-nama="<?= htmlspecialchars($mk->nama) ?>"
                                data-nik="<?= htmlspecialchars($mk->nik ?? '') ?>"
                                data-jenis_skk="<?= htmlspecialchars($mk->jenis_skk ?? '') ?>"
                                data-nomor_skk="<?= htmlspecialchars($mk->nomor_skk ?? '') ?>"
                                data-masa="<?= htmlspecialchars($mk->masa_berlaku_skk ?? '') ?>"
                                data-penyedia_id="<?= $mk->penyedia_id ?>"
                                style="border-radius:8px;" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Manajer Keuangan -->
<div class="modal fade" id="modalAddManajerK" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius:16px;">
            <form id="formManajerK" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="manajer_id" id="manajer_k_id" value="">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #065f46, #0f4c75); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-user-tie mr-2"></i><span id="modalTitleK">Tambah Manajer Keuangan</span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Perusahaan</label>
                                <select name="penyedia_id" id="modal_k_penyedia_id" class="form-control" required style="border-radius:10px;">
                                    <option value="">-- Pilih Perusahaan --</option>
                                    <?php foreach($penyedia_list as $p): ?>
                                    <option value="<?= $p->id ?>"><?= $p->nama_perusahaan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nama Lengkap</label>
                                <input type="text" name="nama" id="modal_k_nama" class="form-control" required placeholder="Nama Manajer Keuangan" style="border-radius:10px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">NIK</label>
                                <input type="text" name="nik" id="modal_k_nik" class="form-control" placeholder="16 Digit NIK" style="border-radius:10px;" maxlength="16">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Jenis SKK</label>
                                <input type="text" name="jenis_skk" id="modal_k_jenis_skk" class="form-control" placeholder="Contoh: SKK Akuntansi" style="border-radius:10px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Nomor SKK</label>
                                <input type="text" name="nomor_skk" id="modal_k_nomor_skk" class="form-control" placeholder="Nomor SKK" style="border-radius:10px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold">Masa Berlaku SKK</label>
                                <input type="text" name="masa_berlaku_skk" id="modal_k_masa" class="form-control datepicker-modal-k" placeholder="dd/mm/yyyy" style="border-radius:10px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold"><i class="fas fa-id-card text-info mr-1"></i>Upload File KTP <small class="text-muted">(pdf/jpg/png, maks 2MB)</small></label>
                                <input type="file" name="file_ktp" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold"><i class="fas fa-certificate text-success mr-1"></i>Upload File SKK <small class="text-muted">(pdf/jpg/png, maks 2MB)</small></label>
                                <input type="file" name="file_skk" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tableManajerKeuangan').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: { previous: "Prev", next: "Next" }
        },
        columnDefs: [{ orderable: false, targets: [7, 8] }]
    });

    $('#filterPenyedia').on('change', function() {
        var pid = $(this).val();
        var url = '<?= base_url($module."/manajer_keuangan") ?>';
        if (pid) url += '?penyedia_id=' + pid;
        window.location.href = url;
    });

    $(document).on('click', '.btn-edit-manajer-k', function() {
        var btn = $(this);
        $('#modalTitleK').text('Edit Manajer Keuangan');
        $('#manajer_k_id').val(btn.data('id'));
        $('#modal_k_penyedia_id').val(btn.data('penyedia_id'));
        $('#modal_k_nama').val(btn.data('nama'));
        $('#modal_k_nik').val(btn.data('nik'));
        $('#modal_k_jenis_skk').val(btn.data('jenis_skk'));
        $('#modal_k_nomor_skk').val(btn.data('nomor_skk'));
        var masa = btn.data('masa');
        if (masa && masa != '0000-00-00') {
            var d = new Date(masa);
            var formatted = ('0'+d.getDate()).slice(-2) + '/' + ('0'+(d.getMonth()+1)).slice(-2) + '/' + d.getFullYear();
            $('#modal_k_masa').val(formatted);
        } else {
            $('#modal_k_masa').val('');
        }
        $('#modalAddManajerK').modal('show');
    });

    $('#modalAddManajerK').on('hidden.bs.modal', function() {
        $('#modalTitleK').text('Tambah Manajer Keuangan');
        $('#manajer_k_id').val('');
        $('#formManajerK')[0].reset();
    });

    $('.datepicker-modal-k').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        orientation: 'bottom auto'
    });

    $('#formManajerK').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        var id = $('#manajer_k_id').val();
        var url = '<?= base_url($module."/manajer_keuangan_save") ?>';
        if (id) url = '<?= base_url($module."/manajer_keuangan_update/") ?>' + id;

        $.ajax({
            url: url,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
                    setTimeout(function() { window.location.reload(); }, 1500);
                    $('#modalAddManajerK').modal('hide');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan.' });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal terhubung ke server.' });
            }
        });
    });
});
</script>
