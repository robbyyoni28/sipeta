<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit mr-2"></i>Recording Paket Pemenang Tender <?= isset($jenis_tender) && $jenis_tender == 'konsultansi' ? 'Konsultansi' : '' ?></h1>
    <a href="<?= base_url($module.'/input_pemenang') ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-home fa-sm"></i> Dashboard
    </a>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success border-left-success shadow alert-dismissible fade show">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close text-white">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger border-left-danger shadow alert-dismissible fade show">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close text-white">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<form action="<?= base_url($module.'/simpan_pemenang') ?>" method="POST" id="form-pemenang" enctype="multipart/form-data">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    <?php if(isset($jenis_tender) && $jenis_tender == 'konsultansi'): ?>
        <input type="hidden" name="jenis_tender" value="konsultansi">
    <?php endif; ?>
    
<!-- 1. Informasi Paket -->
    <div class="card shadow mb-4 border-0 overflow-hidden">
        <div class="card-header py-3 bg-gradient-primary text-white border-0">
            <h5 class="m-0 font-weight-800"><i class="fas fa-file-contract mr-2"></i>1. Informasi Paket & Tender</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Nama Satker</label>
                        <input type="text" name="satuan_kerja" class="form-control form-control-lg bg-light border-0 shadow-none" required placeholder="Contoh: Dinas PUPR Kabupaten ..." style="border-radius: 12px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Judul Paket Pekerjaan</label>
                        <textarea name="judul_paket" class="form-control bg-light border-0 shadow-none" rows="1" required placeholder="Masukkan judul paket pekerjaan..." style="border-radius: 12px;"></textarea>
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Nama POKMIL</label>
                        <input type="text" name="nama_pokmil" class="form-control bg-light border-0" placeholder="Pokmil 4" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kode Tender</label>
                        <input type="text" name="kode_tender" id="kode_tender" class="form-control kode-check bg-light border-0" required placeholder="1234567" style="border-radius: 10px;">
                        <div class="feedback-kode-inline"></div>
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Tanggal BAHP</label>
                        <input type="text" name="tanggal_bahp" class="form-control bg-light border-0 datepicker" required style="border-radius: 10px;" placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Tahun Anggaran</label>
                        <input type="number" name="tahun_anggaran" id="tahun_anggaran" class="form-control bg-light border-0 font-weight-bold text-primary" value="<?= date('Y') ?>" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-5 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-primary">Pemenang Tender (Penyedia)</label>
                        <input type="text" name="nama_penyedia" class="form-control border-primary shadow-sm" required placeholder="Ketik nama perusahaan..." style="border-radius: 12px; border-width: 2px;">
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">HPS (Nilai Paket)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0 text-primary font-weight-bold" style="border-radius: 12px 0 0 12px;">Rp</span>
                            </div>
                            <input type="text" name="hps" class="form-control bg-light border-0 rupiah font-weight-bold" required placeholder="0" style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kualifikasi Usaha</label>
                        <select name="kualifikasi" id="kualifikasi" class="form-control bg-light border-0" style="border-radius: 10px;">
                            <option value="Kecil" selected>Usaha Kecil</option>
                            <option value="Non Kecil">Menengah / Besar</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 2. Personel Lapangan -->
        <div class="col-md-12">
            <div class="card shadow mb-4 border-0 overflow-hidden">
                <div class="card-header py-3 bg-gradient-info text-white border-0">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user-friends mr-2"></i>2. Personel Tim Pelaksana (Manajer & Ahli K3)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 small">
                            <thead class="bg-light text-uppercase font-weight-bold text-muted" style="font-size: 0.7rem;">
                                <tr>
                                    <th class="px-4 py-3">Jabatan</th>
                                    <th class="py-3">Nama Lengkap & NIK</th>
                                    <th class="py-3">Jenis SKK / Sertifikat</th>
                                    <th class="py-3">No. SKK / Sertifikat</th>
                                    <th class="py-3 px-4" width="10%">Masa Berlaku</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Personel Lapangan -->
                                <tr class="bg-white row-pl">
                                    <td class="font-weight-bold px-4 align-middle">Personel Lapangan</td>
                                    <td class="py-3">
                                        <input type="text" name="personel_lapangan[0][nama]" class="form-control form-control-sm mb-1 bg-light border-0" placeholder="Nama Lengkap" style="border-radius: 8px;">
                                        <input type="text" name="personel_lapangan[0][nik]" class="form-control form-control-sm nik-check bg-light border-0" placeholder="NIK" style="border-radius: 8px;">
                                        <div class="feedback-nik-inline"></div>
                                    </td>
                                    <td class="py-3"><input type="text" name="personel_lapangan[0][jenis_skk]" class="form-control form-control-sm bg-light border-0" placeholder="Jenis SKK" style="border-radius: 8px;"></td>
                                    <td class="py-3">
                                        <input type="text" name="personel_lapangan[0][nomor_skk]" class="form-control form-control-sm skk-check bg-light border-0" placeholder="No. SKK" style="border-radius: 8px;">
                                        <div class="feedback-skk-inline"></div>
                                    </td>
                                    <td class="py-3 px-4"><input type="text" name="personel_lapangan[0][masa_berlaku_skk]" class="form-control form-control-sm bg-light border-0 datepicker" style="border-radius: 8px;" placeholder="dd/mm/yyyy"></td>
                                    <input type="hidden" name="personel_lapangan[0][jabatan]" value="Personel Lapangan">
                                </tr>
                                <!-- MT -->
                                <tr class="bg-light-faded row-mt">
                                    <td class="font-weight-bold px-4 align-middle">Manajer Teknik</td>
                                    <td class="py-3">
                                        <input type="text" name="manajer_teknik[nama]" class="form-control form-control-sm mb-1 bg-light border-0" placeholder="Nama Lengkap" style="border-radius: 8px;">
                                        <input type="text" name="manajer_teknik[nik]" class="form-control form-control-sm nik-check bg-light border-0" placeholder="NIK" style="border-radius: 8px;">
                                        <div class="feedback-nik-inline"></div>
                                    </td>
                                    <td class="py-3"><input type="text" name="manajer_teknik[jenis_skk]" class="form-control form-control-sm bg-light border-0" placeholder="Jenis SKK" style="border-radius: 8px;"></td>
                                    <td class="py-3">
                                        <input type="text" name="manajer_teknik[nomor_skk]" class="form-control form-control-sm skk-check bg-light border-0" placeholder="No. SKK" style="border-radius: 8px;">
                                        <div class="feedback-skk-inline"></div>
                                    </td>
                                    <td class="py-3 px-4"><input type="text" name="manajer_teknik[masa_berlaku_skk]" class="form-control form-control-sm bg-light border-0 datepicker" style="border-radius: 8px;" placeholder="dd/mm/yyyy"></td>
                                </tr>
                                <!-- MK -->
                                <tr class="row-mk">
                                    <td class="font-weight-bold px-4 align-middle">Manajer Keuangan</td>
                                    <td class="py-3">
                                        <input type="text" name="manajer_keuangan[nama]" class="form-control form-control-sm mb-1 bg-light border-0" placeholder="Nama Lengkap" style="border-radius: 8px;">
                                        <input type="text" name="manajer_keuangan[nik]" class="form-control form-control-sm nik-check bg-light border-0" placeholder="NIK" style="border-radius: 8px;">
                                        <div class="feedback-nik-inline"></div>
                                    </td>
                                    <td class="py-3"><input type="text" name="manajer_keuangan[jenis_skk]" class="form-control form-control-sm bg-light border-0" placeholder="Jenis SKK" style="border-radius: 8px;"></td>
                                    <td class="py-3">
                                        <input type="text" name="manajer_keuangan[nomor_skk]" class="form-control form-control-sm skk-check bg-light border-0" placeholder="No. SKK" style="border-radius: 8px;">
                                        <div class="feedback-skk-inline"></div>
                                    </td>
                                    <td class="py-3 px-4"><input type="text" name="manajer_keuangan[masa_berlaku_skk]" class="form-control form-control-sm bg-light border-0 datepicker" style="border-radius: 8px;" placeholder="dd/mm/yyyy"></td>
                                </tr>
                                <!-- K3 -->
                                <tr style="background-color: rgba(247, 37, 133, 0.05);">
                                    <td class="font-weight-bold px-4 align-middle text-danger">Ahli K3 Konstruksi</td>
                                    <td class="py-3">
                                        <input type="text" name="personel_k3[0][nama]" class="form-control form-control-sm mb-1 bg-white border-danger shadow-sm" placeholder="Nama Lengkap" style="border-radius: 8px;">
                                        <input type="text" name="personel_k3[0][nik]" class="form-control form-control-sm nik-check bg-white border-danger shadow-sm" placeholder="NIK" style="border-radius: 8px;">
                                        <div class="feedback-nik-inline"></div>
                                    </td>
                                    <td class="py-3"><input type="text" name="personel_k3[0][jenis_sertifikat_k3]" class="form-control form-control-sm bg-white border-0 shadow-sm" placeholder="Jenis Sertifikat" style="border-radius: 8px;"></td>
                                    <td class="py-3">
                                        <input type="text" name="personel_k3[0][nomor_sertifikat_k3]" class="form-control form-control-sm skk-check bg-white border-0 shadow-sm" placeholder="No. Sertifikat" style="border-radius: 8px;">
                                        <div class="feedback-skk-inline"></div>
                                    </td>
                                    <td class="py-3 px-4"><input type="text" name="personel_k3[0][masa_berlaku_sertifikat]" class="form-control form-control-sm bg-white border-0 shadow-sm datepicker" style="border-radius: 8px;" placeholder="dd/mm/yyyy"></td>
                                    <input type="hidden" name="personel_k3[0][jabatan_k3]" value="Ahli K3 Konstruksi">
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(!isset($jenis_tender) || $jenis_tender !== 'konsultansi'): ?>
    <!-- 3. Peralatan -->
    <div class="card shadow mb-4 border-0 overflow-hidden" id="section-peralatan">
        <div class="card-header py-3 bg-gradient-success text-white border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-tools mr-2"></i>3. Data Peralatan Utama</h6>
            <button type="button" id="btn-add-peralatan" class="btn btn-light btn-sm font-weight-bold rounded-pill">
                <i class="fas fa-plus mr-1"></i> Tambah Jenis Alat
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 small" id="table-peralatan">
                    <thead class="bg-light text-uppercase font-weight-bold text-muted" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4 py-3" width="18%">Nama / Jenis Alat</th>
                            <th class="py-3" width="8%">Jumlah</th>
                            <th class="py-3">Detail Unit (Plat/Seri, Merk & Tipe, Kapasitas, Kepemilikan, Pemilik, Bukti)</th>
                            <th class="py-3 px-4" width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="row-peralatan-template">
                            <td class="px-4 py-3 align-middle">
                                <input type="hidden" name="peralatan[0][nama_alat]" class="peralatan-nama-alat-sync" value="">
                                <input type="text" name="peralatan[0][jenis_alat]" class="form-control form-control-sm bg-light border-0 input-jenis-peralatan" placeholder="Excavator" style="border-radius: 8px;" autocomplete="off">
                            </td>
                            <td class="py-3 align-middle">
                                <input type="number" name="peralatan[0][jumlah]" class="form-control form-control-sm bg-light border-0 text-center qty-peralatan" min="1" value="1" style="border-radius: 8px;">
                            </td>
                            <td class="py-3 align-middle">
                                <div class="peralatan-units">
                                    <div class="unit-item" data-unit="0" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; margin-bottom: 10px; background: #f8fafc;">
                                        <div class="form-row">
                                            <div class="col-md-3 mb-2">
                                                <input type="text" name="peralatan[0][units][0][plat_serial]" class="form-control form-control-sm plat-check bg-white border-0 font-weight-bold" placeholder="No. Plat / Seri" style="border-radius: 8px;">
                                                <div class="feedback-plat-inline"></div>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" name="peralatan[0][units][0][merk]" class="form-control form-control-sm bg-white border-0" placeholder="Merk" style="border-radius: 8px;">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" name="peralatan[0][units][0][tipe]" class="form-control form-control-sm bg-white border-0" placeholder="Tipe" style="border-radius: 8px;">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" name="peralatan[0][units][0][kapasitas]" class="form-control form-control-sm bg-white border-0" placeholder="Kapasitas" style="border-radius: 8px;">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <select name="peralatan[0][units][0][status_kepemilikan]" class="form-control form-control-sm bg-white border-0" style="border-radius: 8px;">
                                                    <option value="Milik Sendiri">Milik Sendiri</option>
                                                    <option value="Sewa">Sewa</option>
                                                    <option value="Sewa Beli">Sewa Beli</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="peralatan[0][units][0][nama_pemilik_alat]" class="form-control form-control-sm bg-white border-0" placeholder="Nama Pemilik Alat" style="border-radius: 8px;">
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="peralatan[0][units][0][bukti_kepemilikan]" class="form-control form-control-sm bg-white border-0" placeholder="Bukti Kepemilikan Alat" style="border-radius: 8px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 align-middle text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-peralatan" style="border-radius: 10px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="text-center mb-5 mt-5">
        <button type="submit" class="btn btn-primary btn-lg shadow-lg px-5 border-0 rounded-pill" style="background: linear-gradient(to right, #4361ee, #3f37c9); padding: 15px 50px; font-weight: 800; letter-spacing: 1px;">
            <i class="fas fa-save mr-2"></i> SIMPAN PAKET PEMENANG
        </button>
    </div>
</form>


<script>
$(document).ready(function() {
    // Format Rupiah while typing
    $('.rupiah').on('keyup', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(new Intl.NumberFormat('id-ID').format(val));
    });

    // Samakan nama_alat dengan jenis_alat (kolom DB NOT NULL; form hanya punya satu kolom input)
    function syncAllPeralatanNamaAlat() {
        $('#table-peralatan tbody tr').each(function() {
            var $row = $(this);
            var j = $row.find('.input-jenis-peralatan').val() || '';
            $row.find('.peralatan-nama-alat-sync').val(j);
        });
    }
    $(document).on('input change', '#table-peralatan .input-jenis-peralatan', function() {
        $(this).closest('tr').find('.peralatan-nama-alat-sync').val($(this).val() || '');
    });
    $('#form-pemenang').on('submit', function() {
        syncAllPeralatanNamaAlat();
    });

    // Dynamic peralatan rows
    let peralatanIndex = 0;
    function reindexPeralatan() {
        $('#table-peralatan tbody tr').each(function(idx) {
            // Update peralatan index
            $(this).find('input, select').each(function() {
                const name = $(this).attr('name');
                if (!name) return;
                const newName = name.replace(/peralatan\[\d+\]/, 'peralatan['+idx+']');
                $(this).attr('name', newName);
            });

            // Update unit index
            $(this).find('.unit-item').each(function(uIdx) {
                $(this).attr('data-unit', uIdx);
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (!name) return;
                    // Update both peralatan index and unit index
                    const newName = name
                        .replace(/peralatan\[\d+\]/, 'peralatan['+idx+']')
                        .replace(/\[units\]\[\d+\]/, '[units]['+uIdx+']');
                    $(this).attr('name', newName);
                });
            });
        });
        peralatanIndex = $('#table-peralatan tbody tr').length - 1;
    }

    function syncUnitCount($row) {
        let qty = parseInt($row.find('.qty-peralatan').val(), 10);
        if (isNaN(qty) || qty < 1) qty = 1;
        $row.find('.qty-peralatan').val(qty);

        const $unitsWrap = $row.find('.peralatan-units');
        const $template = $unitsWrap.find('.unit-item:first').clone();
        let current = $unitsWrap.find('.unit-item').length;

        if (current < qty) {
            // Ambil data dari unit pertama untuk diisi ke unit baru
            const $firstUnit = $unitsWrap.find('.unit-item:first');
            const firstUnitData = {
                plat_serial: $firstUnit.find('input[name*="[plat_serial]"]').val(),
                merk: $firstUnit.find('input[name*="[merk]"]').val(),
                tipe: $firstUnit.find('input[name*="[tipe]"]').val(),
                kapasitas: $firstUnit.find('input[name*="[kapasitas]"]').val(),
                status_kepemilikan: $firstUnit.find('select[name*="[status_kepemilikan]"]').val(),
                nama_pemilik_alat: $firstUnit.find('input[name*="[nama_pemilik_alat]"]').val(),
                bukti_kepemilikan: $firstUnit.find('input[name*="[bukti_kepemilikan]"]').val()
            };
            
            for (let i = current; i < qty; i++) {
                const $newUnit = $template.clone();
                
                // Kosongkan nomor plat/seri untuk unit baru (harus berbeda)
                $newUnit.find('input[name*="[plat_serial]"]').val('').addClass('border-warning');
                $newUnit.find('.feedback-plat-inline').empty();
                
                // Isi data lainnya sama dengan unit pertama
                $newUnit.find('input[name*="[merk]"]').val(firstUnitData.merk);
                $newUnit.find('input[name*="[tipe]"]').val(firstUnitData.tipe);
                $newUnit.find('input[name*="[kapasitas]"]').val(firstUnitData.kapasitas);
                $newUnit.find('select[name*="[status_kepemilikan]"]').val(firstUnitData.status_kepemilikan);
                $newUnit.find('input[name*="[nama_pemilik_alat]"]').val(firstUnitData.nama_pemilik_alat);
                $newUnit.find('input[name*="[bukti_kepemilikan]"]').val(firstUnitData.bukti_kepemilikan);
                
                // Tambahkan indicator unit baru
                $newUnit.css('border-left', '4px solid #ffc107');
                $newUnit.find('.col-md-3').prepend('<small class="badge badge-warning mb-1">Unit Baru</small>');
                
                $unitsWrap.append($newUnit);
                
                // Auto-focus ke plat serial yang kosong
                setTimeout(() => {
                    $newUnit.find('input[name*="[plat_serial]"]').focus();
                }, 100);
            }
        } else if (current > qty) {
            $unitsWrap.find('.unit-item').slice(qty).remove();
        }
        reindexPeralatan();
    }

    $('#btn-add-peralatan').on('click', function() {
        const $last = $('#table-peralatan tbody tr:last');
        const $clone = $last.clone();
        // Kosongkan SEMUA field input & select di baris baru
        $clone.find('input').val('');
        $clone.find('select').prop('selectedIndex', 0);
        $clone.find('.qty-peralatan').val(1);
        $clone.find('.feedback-plat-inline, .feedback-skk-inline, .feedback-nik-inline').html('');
        $clone.find('.is-valid, .border-warning').removeClass('is-valid border-warning');
        $clone.find('.badge-warning').remove();
        $clone.css('border-left', '');
        // Hanya sisakan unit pertama, hapus duplikat unit lainnya
        $clone.find('.peralatan-units .unit-item').slice(1).remove();
        $('#table-peralatan tbody').append($clone);
        reindexPeralatan();
    });

    $(document).on('change keyup', '.qty-peralatan', function() {
        const $row = $(this).closest('tr');
        syncUnitCount($row);
    });
    
    // Remove warning border when user starts typing plat serial
    $(document).on('input', 'input[name*="[plat_serial]"]', function() {
        if ($(this).val().length > 0) {
            $(this).removeClass('border-warning');
            $(this).closest('.unit-item').find('.badge-warning').remove();
            $(this).closest('.unit-item').css('border-left', '1px solid #e2e8f0');
        }
    });

    $(document).on('click', '.btn-remove-peralatan', function() {
        const $rows = $('#table-peralatan tbody tr');
        if ($rows.length <= 1) {
            $(this).closest('tr').find('input').val('');
            $(this).closest('tr').find('.qty-peralatan').val(1);
            $(this).closest('tr').find('.feedback-plat-inline').empty();
            $(this).closest('tr').find('.peralatan-units .unit-item').slice(1).remove();
            reindexPeralatan();
            return;
        }
        $(this).closest('tr').remove();
        reindexPeralatan();
    });

    // Hide / show personel rows based on kualifikasi (segmentasi)
    function applySegmentasiVisibility() {
        const seg = $('#kualifikasi').val();
        if (seg === 'Kecil') {
            $('.row-mt, .row-mk').hide().find('input').val('');
        } else {
            $('.row-mt, .row-mk').show();
        }
    }
    $('#kualifikasi').on('change', applySegmentasiVisibility);
    applySegmentasiVisibility();

    // Auto update kualifikasi based on HPS > 15 Miliar
    $('input[name="hps"]').on('keyup change blur', function() {
        let val = $(this).val().replace(/\./g, '').replace(/,/g, '.');
        let hpsNum = parseFloat(val);
        if(!isNaN(hpsNum)) {
            if(hpsNum > 15000000000) {
                if($('#kualifikasi').val() !== 'Non Kecil') {
                    $('#kualifikasi').val('Non Kecil').trigger('change');
                }
            } else {
                if($('#kualifikasi').val() !== 'Kecil') {
                    $('#kualifikasi').val('Kecil').trigger('change');
                }
            }
        }
    });

    // Function to show detailed duplicate modal
    function showDuplicateModal(type, data, callback) {
        let title, icon, color, details;
        
        if (type === 'personel') {
            title = 'Personel Sudah Digunakan';
            icon = 'fa-user-tie';
            color = 'warning';
            details = `
                <div class="alert alert-warning border-left-warning">
                    <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Personel ini sudah terdaftar di tender lain!</h6>
                    <hr class="my-2">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Nama Personel</small>
                            <strong>${data.nama || '-'}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">NIK</small>
                            <code class="bg-light px-2 py-1">${data.nik || '-'}</code>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Jabatan</small>
                            <span class="badge badge-primary">${data.jabatan || data.jabatan_k3 || '-'}</span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Digunakan di Tender</small>
                            <strong class="text-danger">${data.kode_tender || '-'}</strong>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted d-block">Nama Paket</small>
                            <em>${data.judul_paket || '-'}</em>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Tahun Anggaran</small>
                            <span class="badge badge-dark">${data.tahun_anggaran || '-'}</span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Penyedia</small>
                            <strong>${data.nama_perusahaan || '-'}</strong>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'peralatan') {
            title = 'Peralatan Sudah Digunakan';
            icon = 'fa-truck';
            color = 'warning';
            details = `
                <div class="alert alert-warning border-left-warning">
                    <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Peralatan ini sudah terdaftar di tender lain!</h6>
                    <hr class="my-2">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Jenis Alat</small>
                            <strong>${data.jenis_alat || '-'}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Merk / Tipe</small>
                            <span>${data.merk || '-'} / ${data.tipe || '-'}</span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">No. Seri / Plat</small>
                            <code class="bg-light px-2 py-1">${data.plat || data.plat_serial || '-'}</code>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Kapasitas</small>
                            <span class="badge badge-info">${data.kapasitas || '-'}</span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Digunakan di Tender</small>
                            <strong class="text-danger">${data.kode_tender || '-'}</strong>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted d-block">Nama Paket</small>
                            <em>${data.judul_paket || '-'}</em>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Tahun Anggaran</small>
                            <span class="badge badge-dark">${data.tahun_anggaran || '-'}</span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <small class="text-muted d-block">Penyedia</small>
                            <strong>${data.nama_perusahaan || '-'}</strong>
                        </div>
                    </div>
                </div>
            `;
        }

        let modalHtml = `
            <div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-${color} text-white">
                            <h5 class="modal-title"><i class="fas ${icon} mr-2"></i>${title}</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            ${details}
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-question-circle mr-2"></i>Apakah Anda ingin melanjutkan?</h6>
                                <p class="mb-0 small text-muted">Data ini sudah digunakan di tender tahun ${data.tahun_anggaran || 'ini'}. Jika Anda yakin ingin melanjutkan, klik tombol <strong>"Ya, Lanjutkan"</strong>. Jika tidak, klik <strong>"Batal"</strong> untuk menghapus data ini.</p>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn-mockup-cancel mr-2" data-dismiss="modal" id="btnCancelDuplicate">
                                <i class="fas fa-times mr-1"></i> Batal
                            </button>
                            <button type="button" class="btn-mockup-${color === 'warning' ? 'warning' : 'save'}" id="btnConfirmDuplicate">
                                <i class="fas fa-check mr-1"></i> Ya, Lanjutkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#duplicateModal').remove();
        
        // Append and show modal
        $('body').append(modalHtml);
        $('#duplicateModal').modal('show');

        // Handle confirmation
        $('#btnConfirmDuplicate').on('click', function() {
            $('#duplicateModal').modal('hide');
            callback(true);
        });

        $('#btnCancelDuplicate, #duplicateModal .close').on('click', function() {
            $('#duplicateModal').modal('hide');
            callback(false);
        });

        // Clean up after modal is hidden
        $('#duplicateModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // --- Form Submit: cek duplikat dulu (NIK, No SKK, Kode Tender, No Seri Kendaraan). Jika ada duplikat = tampil modal; jika tidak = langsung simpan ---
    let isSubmitting = false;
    $('#form-pemenang').on('submit', function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;
        performAjaxSubmit(this);
    });

    function performAjaxSubmit(form, forceSave = false) {
        syncAllPeralatanNamaAlat();
        let formData = new FormData(form);
        if (forceSave) {
            formData.append('force_save', '1');
        }
        let url = $(form).attr('action');

        const $submitBtn = $(form).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function(res) {
                console.log('SUBMIT RESPONSE:', res);
                if (res && res.csrfHash) {
                    $(form).find('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(res.csrfHash);
                }

                if (res.status === 'duplicate') {
                    console.warn('Duplicate detected on submit!', res.duplicates);
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET PEMENANG');
                    showBulkDuplicateModal(res.duplicates, function(confirmed) {
                        if (confirmed) {
                            isSubmitting = true;
                            performAjaxSubmit(form, true);
                        } else {
                            isSubmitting = false;
                        }
                    });
                    return;
                }

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    setTimeout(() => {
                        window.location.href = '<?= base_url($module."/input_pemenang") ?>';
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan sistem.'
                    });
                    isSubmitting = false;
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET PEMENANG');
                }
            },
            error: function(xhr) {
                let message = 'Gagal terhubung ke server.';
                if (xhr && xhr.responseJSON) {
                    message = xhr.responseJSON.message || xhr.responseJSON.error || message;
                } else if (xhr && xhr.responseText) {
                    try {
                        const parsed = JSON.parse(xhr.responseText);
                        message = parsed.message || parsed.error || message;
                    } catch (e) {
                        const txt = String(xhr.responseText).replace(/<[^>]*>/g, '').trim();
                        if (txt) message = txt;
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                isSubmitting = false;
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET PEMENANG');
            }
        });
    }

    // Function to show bulk duplicate summary modal (NIK / No SKK / Kode Tender / No Seri Kendaraan sama)
    function showBulkDuplicateModal(duplicates, callback) {
        console.log('Showing Bulk Duplicate Modal with:', duplicates);
        let rows = '';
        duplicates.forEach(function(d) {
            let detail = d.detail || {};
            let namaOrAlat = detail.nama || detail.jenis_alat || detail.nama_alat || d.identifier;
            let tenderInfo = detail.kode_tender ? ('Tender: ' + detail.kode_tender) : '';
            
            // Highlight the matched field
            let matchLabel = d.matched_by ? ('<span class="badge badge-danger ml-1">' + d.matched_by + ' Sama</span>') : '';
            
            rows += '<tr>' +
                '<td><span class="badge badge-primary">' + (d.type || 'Duplikat') + '</span>' + matchLabel + '</td>' +
                '<td><code>' + (d.identifier || '-') + '</code></td>' +
                '<td>' +
                '<div class="small font-weight-bold text-dark">' + namaOrAlat + '</div>' +
                (tenderInfo ? '<div class="small text-danger">' + tenderInfo + '</div>' : '') +
                (detail.judul_paket ? '<div class="small text-muted">' + (detail.judul_paket || '') + '</div>' : '') +
                '</td>' +
                '<td><span class="small font-weight-bold">' + (detail.nama_perusahaan || '-') + '</span></td>' +
                '</tr>';
        });

        let modalHtml = `
            <div class="modal fade" id="bulkDuplicateModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-white border-0">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Peringatan Duplikasi Data</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-dark">Sistem menemukan bahwa beberapa data yang Anda masukkan telah digunakan di tender lain pada tahun yang sama:</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-striped small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tipe</th>
                                            <th>Identifier (NIK/SKK/Plat)</th>
                                            <th>Detail Penggunaan Sebelumnya</th>
                                            <th>Penyedia</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            </div>
                            <div class="alert alert-light border mt-3 mb-0">
                                <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-question-circle mr-2"></i>Tetap Simpan?</h6>
                                <p class="mb-0 small text-muted">Data di atas tercatat sudah digunakan di tender lain. Apakah Anda yakin ingin melanjutkan penyimpanan paket pemenang ini?</p>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 p-3">
                            <button type="button" class="btn-mockup-cancel mr-2" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Batal & Cek Lagi
                            </button>
                            <button type="button" class="btn-mockup-warning" id="confirmBulkSave">
                                <i class="fas fa-check mr-1"></i> Ya, Tetap Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#bulkDuplicateModal').remove();
        $('body').append(modalHtml);
        $('#bulkDuplicateModal').modal('show');

        $('#confirmBulkSave').on('click', function() {
            $('#bulkDuplicateModal').data('confirmed', true);
            $('#bulkDuplicateModal').modal('hide');
            callback(true);
        });

        // CRITICAL: Handle modal dismissal (Batal, X, click outside)
        $('#bulkDuplicateModal').on('hidden.bs.modal', function () {
            // If callback wasn't triggered by confirm button yet, assume cancel
            if (!$(this).data('confirmed')) {
                isSubmitting = false;
                $('#form-pemenang button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET PEMENANG');
                callback(false);
            }
            $(this).remove();
        });
    }

    // Keep individual checks for real-time feedback (optional but good for UX)
    $(document).on('change input', '.nik-check, .skk-check, .plat-check, .kode-check', function() {
        let input = $(this);
        let val = input.val();
        let isPlat = input.hasClass('plat-check');
        let isKode = input.hasClass('kode-check');
        let isSkk = input.hasClass('skk-check');
        let feedback = input.siblings(isPlat ? '.feedback-plat-inline' : (isKode ? '.feedback-kode-inline' : (isSkk ? '.feedback-skk-inline' : '.feedback-nik-inline')));
        
        // Prevent "stray" data: if NIK is changed, clear SKK/Certificate
        // Moved to separate 'change' event below to avoid aggressive clearing while typing
        
        if (val.length < 3) return;

        $.ajax({
            url: '<?= base_url($module."/check_bulk_duplicates") ?>',
            method: 'POST',
            data: { 
                kode_tender: isKode ? val : '',
                personel: (isPlat || isKode) ? [] : [{
                    nik: isSkk ? '' : val, 
                    no_skk: isSkk ? val : ''
                }],
                peralatan: isPlat ? [{plat: val}] : [],
                tahun: $('#tahun_anggaran').val(),
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>' 
            },
            dataType: 'JSON',
            success: function(res) {
                console.log('Real-time check result:', res);
                if (res.status === 'duplicate') {
                    input.addClass('border-warning');
                    let btnAutofill = '';
                    if (!isKode && res.duplicates && res.duplicates.length > 0) {
                        let masterData = res.duplicates[0].detail;
                        // Store data in the button
                        let dataStr = encodeURIComponent(JSON.stringify(masterData));
                        btnAutofill = `<br><button type="button" class="btn btn-xs btn-outline-warning mt-1 btn-autofill-row" data-master="${dataStr}" style="font-size: 0.65rem; padding: 2px 5px;"><i class="fas fa-magic mr-1"></i> Gunakan Data Terdeteksi</button>`;
                    }
                    feedback.html('<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Terdeteksi Duplikat'+btnAutofill+'</small>');
                } else {
                    input.removeClass('border-warning').addClass('is-valid');
                    feedback.html('<small class="text-success"><i class="fas fa-check-circle"></i> Aman</small>');
                }
            },
            error: function(err) {
                console.error('Real-time check error:', err);
            }
        });
    });

    // Separated Clear Logic to avoid clearing while typing (only on change/blur)
    $(document).on('change', '.nik-check', function() {
        let input = $(this);
        let row = input.closest('tr');
        // Only clear if the NIK is longer than 3 or empty (indicating a real change intent)
        if (input.val().length > 3 || input.val().length === 0) {
            row.find('input[name*="nomor_skk"], input[name*="nomor_sertifikat_k3"], input[name*="nama"]').val('');
            row.find('.is-valid').removeClass('is-valid');
            row.find('.feedback-skk-inline, .feedback-nik-inline').empty();
            row.find('.skk-check, .nik-check').removeClass('border-warning');
        }
    });

    // Handle Autofill Click
    $(document).on('click', '.btn-autofill-row', function() {
        let btn = $(this);
        let rawData = btn.data('master');
        console.log('Autofill clicked. Raw data:', rawData);
        let data = JSON.parse(decodeURIComponent(rawData));
        console.log('Parsed data:', data);
        let row = btn.closest('tr');
        let unit = btn.closest('.unit-item');
        
        // Check if it's personel or peralatan
        // Ensure we check for properties exactly as returned by PHP
        if (data.nik && (data.nomor_skk || data.nomor_sertifikat_k3 || data.jenis_skk || data.jabatan || data.jabatan_k3)) {
            // It's personel (Lapangan or K3)
            row.find('input[name*="nama"]').val(data.nama);
            if (data.jenis_skk) row.find('input[name*="jenis_skk"]').val(data.jenis_skk);
            if (data.nomor_skk) row.find('input[name*="nomor_skk"]').val(data.nomor_skk);
            if (data.pengalaman_tahun) row.find('input[name*="pengalaman_tahun"]').val(data.pengalaman_tahun);
            if (data.masa_berlaku_skk) row.find('input[name*="masa_berlaku_skk"]').val(data.masa_berlaku_skk);
            
            // If K3
            if (data.jenis_sertifikat_k3) row.find('input[name*="jenis_sertifikat_k3"]').val(data.jenis_sertifikat_k3);
            if (data.nomor_sertifikat_k3) row.find('input[name*="nomor_sertifikat_k3"]').val(data.nomor_sertifikat_k3);
            if (data.masa_berlaku_sertifikat) row.find('input[name*="masa_berlaku_sertifikat"]').val(data.masa_berlaku_sertifikat);
            
            feedback_toast('Data Personel berhasil diisi otomatis!');
        } else if (data.plat_serial) {
            // It's peralatan
            row.find('input[name*="jenis_alat"]').first().val(data.jenis_alat);
            let scope = unit.length ? unit : row;
            scope.find('input[name*="plat_serial"]').val(data.plat_serial);
            scope.find('input[name*="merk"]').val(data.merk);
            scope.find('input[name*="tipe"]').val(data.tipe);
            scope.find('input[name*="kapasitas"]').val(data.kapasitas);
            scope.find('select[name*="status_kepemilikan"]').val(data.status_kepemilikan);
            scope.find('input[name*="nama_pemilik_alat"]').val(data.nama_pemilik_alat);
            scope.find('input[name*="bukti_kepemilikan"]').val(data.bukti_kepemilikan);
            
            feedback_toast('Data Peralatan berhasil diisi otomatis!');
        }
    });

    function feedback_toast(msg) {
        // Simple visual feedback
        let toast = $(`<div class="fixed-bottom p-3" style="z-index: 9999; right: 16px; left: auto; transform: none; width: auto; max-width: calc(100vw - 32px); pointer-events: none;">
                        <div class="alert alert-success shadow border-0 rounded-pill px-4 py-2">
                            <i class="fas fa-check-circle mr-2"></i> ${msg}
                        </div>
                      </div>`);
        $('body').append(toast);
        setTimeout(() => toast.fadeOut(() => toast.remove()), 3000);
    }
});
</script>



