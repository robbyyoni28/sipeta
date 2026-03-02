            </div>
        </div>
    </div>
    </div>
<script>
        $(document).ready(function() {
            $('.datatable').DataTable({
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
                }
            });

            if ($('.select2').length > 0) {
                $('.select2').select2({
                    placeholder: "Pilih data...",
                    allowClear: true,
                    width: '100%'
                });
            }
            
            // Initialize Datepicker
            if ($('.datepicker').length > 0) {
                $('.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    orientation: 'bottom',
                    language: 'en',
                    startView: 0,
                    minViewMode: 0,
                    maxViewMode: 2,
                    weekStart: 1,
                    forceParse: true
                });
                
                // Force format on input event
                $('.datepicker').on('input change', function() {
                    var val = $(this).val();
                    if (val && val.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                        // Valid dd/mm/yyyy format
                        $(this).val(val);
                    }
                });
            }
        });
        
        // NOTIFICATION SYSTEM
        function loadNotifications() {
            $.get('<?= base_url('admin/notifikasi/get_count') ?>', function(res) {
                const count = res.count || 0;
                $('#notifBadge').text(count);
                if (count > 0) {
                    $('#notifBadge').show();
                } else {
                    $('#notifBadge').hide();
                }
            });
        }

        function renderNotificationDropdown() {
            $('#notifList').html('<div class="p-3 text-center text-muted">Memuat...</div>');
            $.get('<?= base_url('admin/notifikasi/get_list') ?>', function(res) {
                const notifications = res.notifications || [];

                if (notifications.length === 0) {
                    $('#notifList').html('<div class="p-3 text-center text-muted">Tidak ada notifikasi</div>');
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
                $('#notifList').html('<div class="p-3 text-center text-danger">Gagal memuat notifikasi</div>');
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
