# Excel Export Feature - Quick Start Guide

## What Was Added?

A new Excel export feature that allows users to download pengajuan (submission) reports in CSV format.

## Where to Find It?

On any pengajuan detail page (`/pengajuan/{id}/open`), you'll find the Excel button in the action buttons row:

```
[← Kembali] [PDF] [Excel]
```

The **blue "Excel"** button downloads the report.

## What's in the Excel File?

The exported CSV file contains:

### Part 1: Pengajuan Details
| Field | Example |
|-------|---------|
| Kode RUP | 2024.1.001 |
| Nama Paket | Pengadaan Barang A |
| Perangkat Daerah | Dinas Pendidikan |
| Rekening Kegiatan | 5.01.01.00.00 |
| Sumber Dana | APBD Tahun 2024 |
| Pagu Anggaran | 500.000.000 |
| Pagu HPS | 480.000.000 |
| Jenis Pengadaan | Barang |
| Metode Pengadaan | Tender Terbuka |
| Tanggal Pengajuan | 15/11/2024 09:30:45 |
| Status | Menunggu Verifikator |

### Part 2: Daftar Berkas (Files List)
| No | Nama File | Status | Tanggal Upload |
|----|-----------|--------|----------------|
| 1 | Surat Permohonan.pdf | Sesuai | 15/11/2024 09:30:45 |
| 2 | RUP.pdf | Perlu Perbaikan | 15/11/2024 09:35:20 |
| 3 | Dokumen Teknis.pdf | Belum Direviu | 15/11/2024 09:40:15 |

## How to Use

### Step 1: Open Pengajuan Detail
- Click on any pengajuan in the dashboard

### Step 2: Click Excel Button
- Look for the blue **Excel** button in the action buttons
- Click it

### Step 3: Download & Open
- Browser downloads: `pengajuan_[ID].csv`
- Open in Excel, Google Sheets, or LibreOffice Calc

### Step 4: View Report
- Spreadsheet opens with all pengajuan information
- Share or print as needed

## File Format

- **Format**: CSV (Comma-Separated Values)
- **Encoding**: UTF-8 (supports Indonesian characters)
- **Delimiter**: Semicolon (;) - works better with European/Asian Excel versions
- **Filename**: `pengajuan_[ID].csv` (e.g., `pengajuan_123.csv`)

## Status Meanings

### Pengajuan Status
- **Menunggu Verifikator** - Waiting for verifier review
- **Tidak Disetujui Verifikator** - Rejected by verifier
- **Menunggu Kepala UKPBJ** - Waiting for UKPBJ head review
- **Menunggu Reviu Pokja** - Waiting for selection committee review
- **Siap Ditayangkan** - Ready to be published
- **System stops** - No updates for 3 days (auto-expired)

### File Status
- **Belum Direviu** - Not yet reviewed
- **Sesuai** - Approved / Compliant
- **Perlu Perbaikan** - Needs revision
- **Tidak Diterima** - Rejected

## Compatibility

Works with:
- ✅ Microsoft Excel
- ✅ Google Sheets
- ✅ LibreOffice Calc
- ✅ OpenOffice
- ✅ Numbers (Mac)
- ✅ Any spreadsheet application

## Tips

1. **Formatting**: Open in Excel, then use Format menu to add colors/borders
2. **Sharing**: Export as PDF from Excel for sharing read-only versions
3. **Printing**: Format in Excel first, then Print with page breaks
4. **Filtering**: Use Excel's filter feature to find specific files
5. **Copy-Paste**: All data is easily copy-paste friendly

## If File Opens in Wrong Format

If the CSV opens as one column instead of multiple columns:
1. In Excel: Data → Text to Columns
2. Choose Delimited
3. Select Semicolon (;) as delimiter
4. Click Finish

## Troubleshooting

### File Downloads as `.csv` but Says "Unknown Format"
- Right-click the file → Open With → Choose Excel
- Or change extension to `.xlsx` if you have an auto-converter

### Indonesian Characters Look Like ???
- Make sure Excel is set to UTF-8 encoding
- Try opening with LibreOffice Calc instead

### Button Doesn't Work
- Make sure you're viewing a pengajuan detail page
- Check browser console (F12) for errors
- Verify pengajuan ID is valid

## Future Enhancements

The development team plans to add:
- XLSX format (native Excel format)
- Custom date filters for bulk export
- Email delivery of reports
- Export history and tracking
- Multiple sheet export

## Keyboard Shortcuts

Once file is open in Excel:
- **Ctrl+Home** - Go to beginning
- **Ctrl+End** - Go to end
- **Ctrl+F** - Find text
- **Ctrl+Print** - Print
- **Ctrl+S** - Save

---

**Commit**: a152c7b (Feature added Nov 16, 2025)
