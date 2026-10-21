# 💰 KeuanganKeluarga - Aplikasi Anggaran & Keuangan Rumah Tangga

Aplikasi website modern berbasis **Laravel 12 + Tailwind CSS + Chart.js** yang dirancang khusus untuk membantu mengelola anggaran, memantau arus kas, dan mencapai target tabungan keluarga dengan visual yang informatif dan elegan.

---

## 🌟 Fitur Utama

1. **📊 Dashboard Visual & Analisis Finansial**
   - **4 Kartu Ringkasan:** Total Kekayaan Saldo (semua kas & bank), Pemasukan Bulan Ini, Pengeluaran Bulan Ini, dan Sisa Bersih Tabungan (Net Cashflow) beserta *Savings Rate (%)*.
   - **Grafik Tren Arus Kas (6 Bulan):** Perbandingan visual pemasukan vs pengeluaran antar bulan (*Bar Chart*).
   - **Grafik Proporsi Kategori:** Komposisi pos pengeluaran terbesar bulan berjalan (*Doughnut Chart*).
   - **Grafik Kontribusi Keluarga:** Proporsi pengeluaran berdasarkan masing-masing anggota keluarga.
   - **Pemantau Batas Anggaran:** Indikator bar visual (Hijau aman, Kuning waspada, Merah jika melebihi batas).

2. **💳 Pencatatan Transaksi Cerdas**
   - Mendukung 3 jenis arus dana: **Pemasukan (+)**, **Pengeluaran (-)**, dan **Transfer Antar Rekening (⇄)**.
   - Dilengkapi filter fleksibel: berdasarkan kata kunci keterangan, jenis transaksi, kategori, anggota keluarga, dan rekening asal/tujuan.
   - Penghitungan otomatis saldo setiap rekening/dompet.

3. **🎯 Perencanaan Anggaran Bulanan (Budget Planner)**
   - Tetapkan batas alokasi pengeluaran per kategori (contoh: Dapur, Listrik/Wifi, Pendidikan Anak, Cicilan KPR, Bensin, Sedekah).
   - Fitur **"Salin dari Bulan Lalu"** dalam satu klik untuk mempermudah perencanaan tiap awal bulan.
   - Notifikasi dan peringatan otomatis jika pengeluaran melebihi anggaran (*Over-budget*).

4. **🏦 Dompet & Rekening Multi-Kas**
   - Kelola berbagai tempat penyimpanan dana keluarga: Rekening Bank (BCA, Mandiri, BNI, dll), Dompet Tunai / Brankas Fisik, Tabungan Khusus, dan E-Wallet (GoPay, OVO, ShopeePay).
   - Saldo berjalan (*real-time running balance*) otomatis terkalkulasi.

5. **👨‍👩‍👧‍👦 Multi-Anggota Keluarga**
   - Catat siapa yang membelanjakan atau menghasilkan dana (Ayah, Ibu, Kakak, Adik).
   - Warna avatar khusus untuk memudahkan identifikasi transaksi.

6. **🌱 Rencana & Target Tabungan Impian (Savings Goals)**
   - Buat tujuan tabungan bersama (misal: Dana Darurat 6 Bulan, Qurban Idul Adha, Liburan Akhir Tahun, Renovasi Rumah).
   - Dilengkapi progress bar visual persentase dan tombol cepat **+ Tambah Setoran**.

7. **🏷️ Pos Kategori Fleksibel**
   - Tambah dan ubah kategori pengeluaran dan pemasukan sesuai kebutuhan rumah tangga Anda.

8. **📄 Laporan Finansial Siap Cetak / Simpan PDF**
   - Ringkasan buku kas bulanan dengan format cetak profesional (*print-friendly layout*).
   - Dilengkapi rincian persentase dan kolom tanda tangan Kepala & Bendahara Keluarga.

---

## 🚀 Cara Menjalankan Aplikasi

Aplikasi berada di direktori:
```
C:\xampp\htdocs\keuangan-keluarga
```

### Opsi 1: Menggunakan Built-in Laravel Server (Rekomendasi Cepat)

1. Buka Terminal / PowerShell / Command Prompt.
2. Masuk ke folder proyek:
   ```bash
   cd C:\xampp\htdocs\keuangan-keluarga
   ```
3. Jalankan server Laravel:
   ```bash
   php artisan serve
   ```
4. Buka browser dan akses:
   👉 **`http://127.0.0.1:8000`**

---

### Opsi 2: Menggunakan Apache XAMPP

1. Buka **XAMPP Control Panel**, lalu klik **Start** pada Apache.
2. Buka browser dan akses URL:
   👉 **`http://localhost/keuangan-keluarga/public`**

*(Frontend CSS & JS sudah dikompilasi ke dalam folder `public/build/assets/` sehingga dapat langsung dibuka tanpa perlu menjalankan npm lagi).*

---

## 🛠️ Data Awal (Demo Seeder)

Aplikasi telah terisi data awal realistis untuk keluarga Indonesia, meliputi:
- **Anggota Keluarga:** Ayah (Budi), Ibu (Siti), Kakak (Rian), Adik (Zahra).
- **Rekening:** BCA Payroll, Mandiri Tabungan Keluarga, Dompet Tunai, GoPay & OVO.
- **Kategori:** 13 Pos kategori pemasukan & pengeluaran lengkap.
- **Target Tabungan:** Dana Darurat, Qurban, Liburan Akhir Tahun, Renovasi Kamar.
- **Transaksi & Anggaran Bulan Berjalan:** Siap diuji coba dan langsung dapat ditambahkan atau diubah.

Jika ingin mereset kembali ke data awal:
```bash
php artisan migrate:fresh --seed
```
