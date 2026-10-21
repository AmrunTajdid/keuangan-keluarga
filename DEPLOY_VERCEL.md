# 🚀 Panduan Hosting KeuanganKeluarga di Vercel & Push ke GitHub

Semua file konfigurasi serverless untuk Vercel telah disiapkan secara lengkap:
1. `vercel.json` (Runtime `vercel-php@0.7.4`, routing otomatis, dan env produksi)
2. `api/index.php` (Serverless handler dengan `/tmp/storage` dan otomatis copy `database.sqlite` ke `/tmp`)
3. `bootstrap/app.php` (Dynamic storage path untuk Vercel)
4. `.vercelignore` (Pencegahan upload file development yang tidak perlu)
5. `public/build` (Asset CSS & Chart.js sudah dikompilasi)

---

## Langkah 1: Buat Repository di GitHub & Push

Karena repository harus dibuat terlebih dahulu di akun GitHub Anda:

1. Buka browser dan buka link:
   👉 **[https://github.com/new](https://github.com/new)**
2. Isi **Repository name**:
   ```
   keuangan-keluarga
   ```
3. Pilih **Public** atau **Private** (sesuai keinginan Anda).
4. **JANGAN centang** *"Add a README file"*, *"Add .gitignore"*, atau *"Choose a license"* (karena proyek lokal kita sudah memilikinya secara lengkap).
5. Klik tombol hijau **"Create repository"**.

6. Buka terminal (PowerShell / Command Prompt) dan jalankan:
   ```bash
   cd C:\xampp\htdocs\keuangan-keluarga
   git push -u origin main
   ```
*(Karena kredensial GitHub akun **AmrunTajdid** sudah tersimpan di Windows Credential Manager, proses push akan langsung terunggah otomatis).*

---

## Langkah 2: Deploy ke Vercel (Gratis Selamanya)

1. Buka **[vercel.com](https://vercel.com)** dan klik **Log In** menggunakan akun **GitHub** Anda.
2. Di halaman Dashboard Vercel, klik tombol **"Add New..."** di kanan atas &rarr; pilih **"Project"**.
3. Di daftar repository GitHub Anda, cari **`keuangan-keluarga`**, lalu klik tombol **"Import"**.
4. Di bagian **Environment Variables** (opsional, sudah ada di `vercel.json`, namun bisa dipastikan):
   - `APP_KEY` : `base64:nwMDynE5d9aWL7yLYQwKhr60i8v/5a0HL4NzDv87aV4=`
   - `APP_ENV` : `production`
   - `APP_DEBUG` : `false`
5. Klik tombol **"Deploy"**.
6. Tunggu sekitar 1–2 menit hingga build selesai. Website aplikasi Keuangan Keluarga Anda akan langsung live di alamat:
   👉 `https://keuangan-keluarga.vercel.app` (atau nama project yang Anda tentukan).
