# Bug Fix Documentation - Peralatan Hilang Saat Tambah Data

## Masalah
Saat menambahkan alat baru di form edit tender, alat yang sudah ada malah hilang/terhapus.

## Root Cause
1. **Logika Delete-Then-Insert**: Fungsi `update_tender_peralatan()` menggunakan logika delete ALL then insert, yang menghapus semua data peralatan yang sudah ada sebelum memasukkan data baru.
2. **Counter JavaScript Conflict**: Counter untuk penambahan baris baru tidak sinkron dengan data yang sudah ada.
3. **Input Name Conflicts**: Saat menambah baris baru, name attributes bisa bentrok dengan data yang sudah ada.

## Solusi yang Diterapkan

### 1. Perbaikan Logika Backend (Admin.php)

#### Sebelumnya:
```php
// Delete existing peralatan for this tender
$this->db->where('tender_id', $tender_id)->delete('tender_peralatan');
// Insert new peralatan...
```

#### Setelah Diperbaiki:
```php
// Get existing peralatan for this tender
$existing_tender_peralatan = $this->db->where('tender_id', $tender_id)
                                     ->get('tender_peralatan')
                                     ->result();

$existing_peralatan_ids = [];
foreach ($existing_tender_peralatan as $existing) {
    $existing_peralatan_ids[] = $existing->peralatan_id;
}

// Process submitted data with update-on-duplicate logic
// Only remove data that's no longer in submitted data
$to_remove = array_diff($existing_peralatan_ids, $submitted_peralatan_ids);
if (!empty($to_remove)) {
    $this->db->where('tender_id', $tender_id)
             ->where_in('peralatan_id', $to_remove)
             ->delete('tender_peralatan');
}
```

### 2. Perbaikan JavaScript Counter (edit_tender.php)

#### Sebelumnya:
```javascript
let peralatanCounter = <?= count($peralatan) ?>;
// Menggunakan counter yang sudah ada, bisa bentrok
```

#### Setelah Diperbaiki:
```javascript
// Get current row count to avoid conflicts
let currentRowCount = $('#peralatan-tbody tr').length;

let newRow = `
    <tr>
        <td>${currentRowCount + 1}</td>
        <td><input type="text" name="peralatan[${currentRowCount}][jenis_alat]" ...></td>
        // ...
    </tr>
`;
```

### 3. Perbaikan Renumber Function

#### Sebelumnya:
```javascript
function renumberTable(tbodyId) {
    $('#' + tbodyId + ' tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}
```

#### Setelah Diperbaiki:
```javascript
function renumberTable(tbodyId) {
    $('#' + tbodyId + ' tr').each(function(index) {
        let newIndex = index;
        $(this).find('td:first').text(newIndex + 1);
        
        // Update input names based on table type
        if (tbodyId === 'peralatan-tbody') {
            $(this).find('input[name^="peralatan["]').each(function() {
                let fieldName = $(this).attr('name').replace(/peralatan\[\d+\]/, 'peralatan[' + newIndex + ']');
                $(this).attr('name', fieldName);
            });
        }
        // ... similar for other tables
    });
}
```

## Hasil Perbaikan

### ✅ Fitur yang Berfungsi:
1. **Add Peralatan**: Bisa menambah alat baru tanpa menghapus yang lama
2. **Edit Peralatan**: Bisa mengedit data alat yang sudah ada
3. **Delete Peralatan**: Bisa menghapus alat tertentu dengan tombol delete
4. **Update-on-Duplicate**: Jika plat serial sama, data master akan diupdate
5. **Dynamic Counter**: Counter otomatis menyesuaikan dengan jumlah baris
6. **Input Name Sync**: Name attributes otomatis di-rename saat delete/add

### ✅ Validasi yang Berfungsi:
1. **NIK Uniqueness**: Mencegah NIK duplikat dalam satu tender
2. **Required Fields**: Validasi field wajib diisi
3. **HPS Format**: Format desimal dengan koma untuk Excel compatibility

## Testing Checklist

### Test Scenario:
1. **Add New Equipment**: 
   - Buka form edit tender
   - Klik "Tambah Peralatan"
   - Isi data alat baru
   - Save → Alat baru tersimpan, alat lama tetap ada

2. **Edit Existing Equipment**:
   - Edit data alat yang sudah ada
   - Save → Perubahan tersimpan

3. **Delete Equipment**:
   - Klik tombol delete pada baris alat
   - Save → Alat terhapus, alat lain tetap ada

4. **Multiple Operations**:
   - Tambah 2 alat baru
   - Edit 1 alat lama
   - Delete 1 alat
   - Save → Semua perubahan tersimpan dengan benar

### Expected Result:
- ✅ Data alat yang sudah ada tidak hilang
- ✅ Alat baru berhasil ditambahkan
- ✅ Edit data tersimpan dengan benar
- ✅ Delete berfungsi sesuai yang dipilih
- ✅ Tidak ada data duplikat
- ✅ Form validation berfungsi

## Files yang Diperbaiki

1. **Controller**: `application/modules/admin/controllers/Admin.php`
   - `update_tender_peralatan()`
   - `update_tender_personel_lapangan()`
   - `update_tender_personel_k3()`

2. **View**: `application/modules/admin/views/edit_tender.php`
   - JavaScript counter logic
   - Add row functions
   - Remove row functions
   - Renumber function

## Best Practices Applied

1. **Update-on-Duplicate Logic**: Lebih efisien daripada delete-insert
2. **Atomic Operations**: Menggunakan transaction untuk data consistency
3. **Client-Server Validation**: Validasi di kedua sisi
4. **Dynamic Form Handling**: JavaScript yang robust untuk dynamic forms
5. **Error Handling**: Proper error messages dan logging

---

**Fixed Date**: 2026-01-30  
**Fixed By**: Senior PHP Developer  
**Status**: ✅ RESOLVED
