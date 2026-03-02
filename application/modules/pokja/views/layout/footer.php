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
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Lanjut",
                        "previous": "Kembali"
                    }
                },
                "pageLength": 10
            };

            // INITIALIZE STATIC TABLES
            $('.datatable').DataTable(window.dtOptions);

            if ($('.select2').length > 0) {
                $('.select2').select2({
                    placeholder: "Pilih data...",
                    allowClear: true,
                    width: '100%'
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
        
        // NOTIFICATION SYSTEM
        function loadNotifications() {
            const lastSeenMs = parseInt(localStorage.getItem('sipeta_notif_last_seen') || '0', 10);
            const since = lastSeenMs > 0 ? Math.floor(lastSeenMs / 1000) : 0;
            $.ajax({
                url: '<?= base_url('pokja/notifikasi/get_count') ?>',
                method: 'GET',
                data: { since: since },
                dataType: 'json'
            }).done(function(res) {
                const count = (res && res.count) ? res.count : 0;
                $('#notifBadge').text(count);
                if (count > 0) {
                    $('#notifBadge').show();
                } else {
                    $('#notifBadge').hide();
                }
            }).fail(function() {
                $('#notifBadge').hide();
            });
        }

        function renderNotificationDropdown() {
            $('#notifList').html('<div class="p-3 text-center text-muted">Memuat...</div>');
            $.ajax({
                url: '<?= base_url('pokja/notifikasi/get_list') ?>',
                method: 'GET',
                dataType: 'json'
            }).done(function(res) {
                const notifications = (res && res.notifications) ? res.notifications : [];

                if (notifications.length === 0) {
                    $('#notifList').empty();
                    return;
                }

                let html = '';
                notifications.forEach(function(notif) {
                    html += `
                        <div class="border-bottom p-3" style="cursor: default;">
                            <div class="d-flex">
                                <div class="mr-3" style="width: 24px; text-align:center;">
                                    <i class="fas ${notif.icon} text-${notif.color}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold">${notif.title}</div>
                                    <div class="small text-muted">${notif.message}</div>
                                    <div class="small text-muted">${notif.time}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#notifList').html(html);
            }).fail(function() {
                $('#notifList').empty();
            });
        }
        
        // Load notifications on page load
        loadNotifications();
        
        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);

        function closeNotifDropdown() {
            $('#notifDropdown').hide();
        }

        // Notification bell click -> toggle dropdown
        $('#notifBell').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            $('#notifBadge').text(0).hide();
            localStorage.setItem('sipeta_notif_last_seen', String(Date.now()));

            const isOpen = $('#notifDropdown').is(':visible');
            if (isOpen) {
                closeNotifDropdown();
                return;
            }

            renderNotificationDropdown();
            $('#notifDropdown').show();
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if ($(e.target).closest('#notifDropdown').length) return;
            if ($(e.target).closest('#notifBell').length) return;
            closeNotifDropdown();
        });
    </script>
</body>
</html>
