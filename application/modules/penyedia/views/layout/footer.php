            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
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
    </script>
</body>
</html>
