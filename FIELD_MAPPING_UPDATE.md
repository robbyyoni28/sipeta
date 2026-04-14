# Field Mapping Update - Personel Tables

## Database Structure

### Personel Lapangan Table
```
id
penyedia_id
nama
nik
jabatan
jenis_skk
nomor_skk
masa_berlaku_skk_sertifikat  ← Field baru yang ditambahkan
file_skk
file_surat_pernyataan
created_by
created_at
updated_at
masa_berlaku_skk
```

### Personel K3 Table
```
id
penyedia_id
nama
nik
jabatan_k3
jenis_sertifikat_k3
nomor_sertifikat_k3
masa_berlaku_sertifikat
file_sertifikat_k3
file_ktp
created_by
created_at
updated_at
```

## Changes Made

### 1. Admin.php - Query Update
**Personel Lapangan Query:**
```php
// Before
$personel_lapangan = $this->db->select('tpl.*, pl.nama, pl.nik, pl.jabatan, pl.jenis_skk, pl.nomor_skk, pl.masa_berlaku_skk')

// After  
$personel_lapangan = $this->db->select('tpl.*, pl.nama, pl.nik, pl.jabatan, pl.jenis_skk, pl.nomor_skk, pl.masa_berlaku_skk, pl.masa_berlaku_skk_sertifikat')
```

### 2. Admin.php - Update Data
**Personel Lapangan Update:**
```php
// Added field masa_berlaku_skk_sertifikat
$this->db->where('id', $personel_id)->update('personel_lapangan', [
    'nama' => $personel['nama'],
    'jabatan' => $personel['jabatan'],
    'jenis_skk' => $personel['jenis_skk'],
    'nomor_skk' => $personel['nomor_skk'],
    'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null,
    'masa_berlaku_skk_sertifikat' => !empty($personel['masa_berlaku_skk_sertifikat']) ? $personel['masa_berlaku_skk_sertifikat'] : null  // ← New field
]);
```

### 3. edit_tender.php - Table Header
**Personel Lapangan Table:**
```html
<!-- Added new column -->
<th>Masa Berlaku Sertifikat</th>
```

### 4. edit_tender.php - Table Body
**Personel Lapangan Input:**
```html
<!-- Added new input field -->
<td>
    <input type="number" name="personel_lapangan[<?= $i-1 ?>][masa_berlaku_skk_sertifikat]" 
           class="form-control form-control-sm" 
           value="<?= html_escape($p->masa_berlaku_skk_sertifikat ?? '') ?>" 
           placeholder="Tahun">
</td>
```

### 5. edit_tender.php - JavaScript Add Row
**Personel Lapangan JavaScript:**
```javascript
// Added new field in dynamic row
<td><input type="number" name="personel_lapangan[${currentRowCount}][masa_berlaku_skk_sertifikat]" class="form-control form-control-sm" placeholder="Tahun"></td>
```

### 6. edit_tender.php - JavaScript Renumber
**Renumber Function:**
```javascript
// Ensure masa_berlaku_skk_sertifikat field is also renumbered when deleting rows
$(this).find('input[name^="personel_lapangan["]').each(function() {
    let fieldName = $(this).attr('name').replace(/personel_lapangan\[\d+\]/, 'personel_lapangan[' + newIndex + ']');
    $(this).attr('name', fieldName);
});
```

## Field Mapping Summary

### Personel Lapangan
| Form Field | Database Field | Type | Notes |
|------------|----------------|------|-------|
| nama | nama | varchar | Nama personel |
| nik | nik | varchar | NIK personel |
| jabatan | jabatan | varchar | Jabatan |
| jenis_skk | jenis_skk | varchar | Jenis SKK |
| nomor_skk | nomor_skk | varchar | Nomor SKK |
| masa_berlaku_skk | masa_berlaku_skk | date | Masa berlaku SKK |
| masa_berlaku_skk_sertifikat | masa_berlaku_skk_sertifikat | int | Masa berlaku sertifikat (tahun) |

### Personel K3
| Form Field | Database Field | Type | Notes |
|------------|----------------|------|-------|
| nama | nama | varchar | Nama personel K3 |
| nik | nik | varchar | NIK personel K3 |
| jabatan_k3 | jabatan_k3 | varchar | Jabatan K3 |
| jenis_sertifikat_k3 | jenis_sertifikat_k3 | varchar | Jenis sertifikat K3 |
| nomor_sertifikat_k3 | nomor_sertifikat_k3 | varchar | Nomor sertifikat K3 |
| masa_berlaku_sertifikat | masa_berlaku_sertifikat | date | Masa berlaku sertifikat |

## Testing Checklist

### Test Personel Lapangan
- [ ] Edit existing personel lapangan data
- [ ] Update masa_berlaku_skk field
- [ ] Update masa_berlaku_skk_sertifikat field
- [ ] Add new personel lapangan with all fields
- [ ] Delete personel lapangan row
- [ ] Verify data saved to database correctly

### Test Personel K3
- [ ] Edit existing personel K3 data
- [ ] Update masa_berlaku_sertifikat field
- [ ] Add new personel K3 with all fields
- [ ] Delete personel K3 row
- [ ] Verify data saved to database correctly

## Important Notes

1. **masa_berlaku_skk_sertifikat** is an integer field (year), while **masa_berlaku_skk** is a date field
2. Personel K3 fields are already correctly mapped to database structure
3. All form inputs now include the new field for personel lapangan
4. JavaScript dynamic row generation includes the new field
5. Renumber function handles the new field correctly

---

**Updated Date**: 2026-01-30  
**Updated By**: Senior PHP Developer  
**Status**: ✅ COMPLETED
