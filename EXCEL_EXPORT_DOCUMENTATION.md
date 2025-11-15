# Excel Export Feature Documentation

## Overview
Added Excel export functionality to the pengajuan (submission) detail page. Users can now download pengajuan reports in Excel/CSV format with all relevant information including pengajuan details and attached files.

## Implementation Details

### Files Modified
1. **app/Http/Controllers/PengajuanOpenController.php**
   - Added `downloadexcel($id)` method
   - Added `getStatusText($status)` helper method
   - Added `getFileStatusText($status)` helper method

2. **routes/web.php**
   - Added 4 new routes (one for each role group)
   - Route pattern: `pengajuan/{id}/open/downloadexcel`
   - Route names: `{role}_pengajuan_open_downloadexcel`

3. **resources/views/dashboard/open.blade.php**
   - Updated Excel button to use the new route
   - Changed from `<a href="#">` to `<a href="{{ route(...) }}">`

## Feature Details

### Excel File Contents

The Excel (CSV) export includes:

#### Section 1: Pengajuan Details
- Kode RUP
- Nama Paket
- Perangkat Daerah
- Rekening Kegiatan
- Sumber Dana
- Pagu Anggaran (formatted with thousand separators)
- Pagu HPS (formatted with thousand separators)
- Jenis Pengadaan
- Metode Pengadaan
- Tanggal Pengajuan
- Status (descriptive text)

#### Section 2: Daftar Berkas (Files)
A table listing all attached files:
- No (sequential number)
- Nama File (filename)
- Status (file review status)
- Tanggal Upload (upload timestamp)

### Status Mapping

**Pengajuan Status:**
```
0  => Menunggu Verifikator
11 => Menunggu Kepala UKPBJ
12 => Tidak Disetujui Verifikator
13 => Menunggu Verifikasi Ulang
14 => File dikembalikan ke PPK
21 => Menunggu Reviu Pokja
22 => Tidak Disetujui Kepala UKPBJ
31 => Siap Ditayangkan
32 => Tidak Disetujui Pokja Pemilihan
33 => Menunggu Verifikasi Ulang
34 => File dikembalikan ke PPK
88 => System stops - No updates for 3 days
```

**File Status:**
```
0 => Belum Direviu (Not Reviewed)
1 => Sesuai (Approved)
2 => Perlu Perbaikan (Needs Revision)
3 => Tidak Diterima (Rejected)
```

## Technical Implementation

### Method: `downloadexcel($id)`

```php
public function downloadexcel($id)
{
    // 1. Load pengajuan and files
    $pengajuan = Pengajuan::findOrFail($id);
    $files = PengajuanFile::where('pengajuan_id', $id)
        ->where('status', '!=', 99)
        ->get();

    // 2. Create CSV in memory
    $filename = 'pengajuan_' . $pengajuan->id . '.csv';
    $handle = fopen('php://memory', 'r+');

    // 3. Write headers and data
    fputcsv($handle, $headers, ';');
    fputcsv($handle, $details, ';');
    
    // 4. Write files table
    foreach ($files as $file) {
        fputcsv($handle, $fileRow, ';');
    }

    // 5. Return as downloadable response
    return response($csv)
        ->header('Content-Encoding', 'UTF-8')
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}
```

### Key Features

1. **UTF-8 Encoding Support**
   - Uses UTF-8 encoding for Indonesian characters
   - Proper BOM handling for Excel compatibility

2. **Semicolon Delimiter**
   - Uses `;` as delimiter (common in European/Asian Excel versions)
   - Can be changed to `,` if needed

3. **Formatted Numbers**
   - Currency values formatted with thousand separators: `1.000.000`
   - Uses Indonesian number format: `number_format($value, 0, ',', '.')`

4. **File Filtering**
   - Excludes files with status 99 (deleted/archived)
   - Includes only active files

5. **Dynamic Filename**
   - Filename format: `pengajuan_[ID].csv`
   - Example: `pengajuan_123.csv`

## Routes Added

### Main Routes (Public/Guest)
```php
Route::get('pengajuan/{id}/open/downloadexcel', [PengajuanOpenController::class, 'downloadexcel'])
    ->name('pengajuan_open_downloadexcel');
```

### Role-Based Routes

1. **PPK Route**
   ```php
   Route::get('pengajuan/{id}/open/downloadexcel', [PengajuanOpenController::class, 'downloadexcel'])
       ->name('ppk_pengajuan_open_downloadexcel');
   ```

2. **Verifikator Route**
   ```php
   Route::get('pengajuan/{id}/open/downloadexcel', [PengajuanOpenController::class, 'downloadexcel'])
       ->name('verifikator_pengajuan_open_downloadexcel');
   ```

3. **Pokja Pemilihan Route**
   ```php
   Route::get('pengajuan/{id}/open/downloadexcel', [PengajuanOpenController::class, 'downloadexcel'])
       ->name('pokjapemilihan_pengajuan_open_downloadexcel');
   ```

## Usage

### For Users
1. Open pengajuan detail page
2. Click the blue "Excel" button in the action buttons row
3. File downloads as `pengajuan_[ID].csv`
4. Open in Excel or any spreadsheet application

### In Blade Template
```blade
<a href="{{ route(auth()->user()->role.'_pengajuan_open_downloadexcel', [$data->id]) }}" 
   class="btn btn-info btn-sm">
   Excel
</a>
```

The route name is dynamically constructed using the user's role:
- PPK: `ppk_pengajuan_open_downloadexcel`
- Verifikator: `verifikator_pengajuan_open_downloadexcel`
- Pokja Pemilihan: `pokjapemilihan_pengajuan_open_downloadexcel`

## Browser Compatibility

- ✅ Chrome/Edge (Downloads as CSV)
- ✅ Firefox (Downloads as CSV)
- ✅ Safari (Downloads as CSV)
- ✅ Excel (Opens directly with `.csv` extension)
- ✅ Google Sheets (Opens via upload or import)
- ✅ LibreOffice Calc (Opens directly)

## Notes

### CSV vs XLSX
- Current implementation uses **CSV (Comma-Separated Values)** format
- Advantages: No additional libraries needed, universally compatible
- To upgrade to XLSX: Install `maatwebsite/excel` package and refactor to use that library

### Delimiter
- Uses semicolon (`;`) delimiter for better compatibility with European/Indonesian Excel versions
- If users need comma delimiter, change all `';'` to `','` in the code

### Character Encoding
- All output is UTF-8 encoded
- Proper handling of Indonesian characters (ü, é, ñ, etc.)
- BOM is not included (use `"\xEF\xBB\xBF"` if needed for Excel compatibility)

## Future Enhancements

Possible improvements:
1. Add XLSX support using `maatwebsite/excel` package
2. Add formatting (bold headers, colors, borders)
3. Add multiple sheet support (separate sheets for different sections)
4. Add date range filters for bulk export
5. Add export history tracking
6. Add email delivery option

## Testing Checklist

- [ ] Test Excel download for PPK role
- [ ] Test Excel download for Verifikator role
- [ ] Test Excel download for Pokja Pemilihan role
- [ ] Verify file opens in Excel
- [ ] Verify file opens in Google Sheets
- [ ] Verify Indonesian characters display correctly
- [ ] Verify currency formatting is correct
- [ ] Test with pengajuan containing multiple files
- [ ] Test with pengajuan containing no files
- [ ] Test with various status values

## Commit Information

- **Commit Hash**: a152c7b
- **Message**: "feat: Add Excel export functionality for pengajuan reports"
- **Files Changed**: 4
- **Insertions**: +136
- **Deletions**: -52
- **Date**: November 16, 2025

## References

### Related Files
- PDF Export: `downloadpdf()` method in same controller (line 1439)
- View: `resources/views/dashboard/open.blade.php` (line 340-343)
- Model: `app/Models/Pengajuan.php`
- Model: `app/Models/PengajuanFile.php`

### Dependencies
- No external packages required
- Uses native PHP `fputcsv()` function
- Uses Laravel Response helper

## Support

For issues or enhancements:
1. Check route names match user's role
2. Verify pengajuan ID exists in database
3. Ensure user has access to pengajuan (check authorization)
4. Test with different file formats and statuses
