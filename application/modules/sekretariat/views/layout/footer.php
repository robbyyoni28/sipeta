            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // GLOBAL DATATABLES OPTIONS
            window.dtOptions = {
                "language": {
                    "search": "🔍 Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "zeroRecords": "Tidak ada data yang cocok",
                    "paginate": {
                        "first": "⏮ Pertama",
                        "last": "Terakhir ⏭",
                        "next": "Lanjut ▶",
                        "previous": "◀ Kembali"
                    }
                },
                "pageLength": 10,
                "order": [[0, 'asc']]
            };

            // INITIALIZE STATIC TABLES
            $('.datatable').DataTable(window.dtOptions);

            // Initialize Select2 for regular dropdowns
            if ($('.select2').length > 0) {
                $('.select2').select2({
                    placeholder: "Pilih data...",
                    allowClear: true,
                    width: '100%'
                });
            }
            
            // Initialize Select2 for minimal inline filters
            if ($('.select2-minimal').length > 0) {
                $('.select2-minimal').select2({
                    minimumResultsForSearch: 5,
                    width: '100%',
                    dropdownAutoWidth: true
                });
            }
            
            // Initialize Datepicker
            if ($('input.datepicker').length > 0) {
                // Remove any previous instances (prevents stale options on pages that re-render)
                try { $('input.datepicker').datepicker('remove'); } catch (e) {}

                $('input.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    orientation: 'auto bottom',
                    container: 'body',
                    zIndexOffset: 10000,
                    language: 'en',
                    startView: 0,
                    minViewMode: 0,
                    maxViewMode: 2,
                    weekStart: 1,
                    forceParse: true
                });
                
                // Force format on input event
                $('input.datepicker').on('input change', function() {
                    var val = $(this).val();
                    if (val && val.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                        // Valid dd/mm/yyyy format
                        $(this).val(val);
                    }
                });
            }
        });

        // GLOBAL AJAX FORM HANDLER
        function ajaxFormSubmit(formSelector, callback) {
            $(document).on('submit', formSelector, function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let formData = new FormData(this);

                // Add CSRF
                formData.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');

                Swal.fire({
                    title: 'Mohon Tunggu...',
                    text: 'Sedang memproses data',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            if (callback) callback(res);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan sistem.'
                            });
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
                    }
                });
            });
        }
    </script>
</body>
</html>
