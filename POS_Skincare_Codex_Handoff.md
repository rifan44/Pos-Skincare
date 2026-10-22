# HAND-OFF PROYEK — Aplikasi POS Skincare

Dokumen ini menjadi konteks awal untuk melanjutkan proyek dari AI sebelumnya ke ChatGPT/Codex. **Informasi progres di bawah bersumber dari laporan sebelumnya dan belum diverifikasi terhadap source code aktif.** Anggap repository dan database lokal sebagai sumber kebenaran.

## 1. Identitas dan tujuan proyek

- **Nama proyek:** Aplikasi Point of Sale (POS) Berbasis Web pada Toko Skincare.
- **Konteks:** Tugas Kerja Praktik (KP), Program Studi Teknik Informatika, Universitas Muhammadiyah Sukabumi (UMMI).
- **Pengembang:** Rifan Maulana.
- **Jenis aplikasi:** Web-based POS dengan arah produk SaaS premium.
- **Tujuan utama:** mendukung transaksi kasir, pencarian/scan barcode, penghitungan kembalian, pengelolaan stok, laporan keuangan, dan cetak struk thermal dengan UI yang konsisten.

## 2. Tech stack yang dilaporkan

- Backend: Laravel (versi perlu diperiksa dari `composer.json` dan `composer.lock`).
- Bahasa backend: PHP (versi perlu diperiksa dari konfigurasi proyek).
- Database: MySQL melalui Laragon.
- Frontend: Blade, Bootstrap 5, Vanilla JavaScript, Font Awesome 6.
- Alert/confirmation UI: SweetAlert2.
- Model yang disebut dalam ringkasan sebelumnya: User, Category, Product, Transaction, TransactionDetail, Setting. **Nama class, nama tabel, kolom, relasi, dan status transaksi wajib diverifikasi dalam kode/migration aktual; jangan membuat asumsi berdasarkan nama ini saja.**

## 3. Global design system

Terapkan pola ini ketika menyentuh halaman yang relevan. Periksa `app.blade.php`, CSS, komponen, dan halaman Kategori/Produk yang sudah ada terlebih dahulu agar aturan ini mengikuti implementasi proyek, bukan membuat desain paralel.

### Warna dan tipografi

- Background halaman: warm off-white/cream `#fdfbf7`.
- Surface/card: `#ffffff`.
- Teks utama: dark/charcoal `#3b3331`.
- Brand primary default: dusty rose `#c78b7d`; warna tema mungkin berasal dari tabel Settings dan harus tetap dinamis.
- Headings H1–H6 dan `.font-serif`: Playfair Display.
- Body/UI: Plus Jakarta Sans.
- Hindari memasukkan ulang CDN/font/style global jika sudah tersedia di layout.

### Pola komponen

- Card: `border-0 shadow-sm rounded-4`.
- Table: `table table-hover align-middle`; gunakan `thead.table-light`.
- Button: `rounded-pill`.
- Untuk halaman non-POS, header menggunakan grid Bootstrap: judul/deskripsi pada `<div class="col-sm-8">` dan action pada `<div class="col-sm-4 text-sm-end">` di dalam row yang sesuai.
- Untuk beberapa tombol aksi tabel, ikuti pola existing yang telah dilaporkan berhasil: wrapper seperti `d-flex justify-content-end gap-2`; sesuaikan lebar kolom tanpa merusak responsive layout.
- POS dapat memakai layout khusus dan tidak wajib mengikuti grid header halaman biasa.
- Alert, empty state, validasi, tombol, modal, tabel, dan pagination harus memakai pola yang konsisten dengan halaman Kategori/Produk yang benar-benar ada.

## 4. Progres yang dilaporkan oleh AI sebelumnya

Poin berikut adalah informasi hand-off, **bukan verifikasi bahwa kode saat ini pasti demikian**.

- Struktur database diklaim sudah memiliki user/role Admin dan Kasir, kategori, produk, transaksi, detail transaksi, dan pengaturan toko.
- `app.blade.php` diklaim memiliki warna tema dinamis dari data Settings.
- `navbar.blade.php` diklaim membedakan menu authenticated user dan guest menggunakan `@auth`/`@else`.
- `landing.blade.php` diklaim memiliki hero dinamis, fitur, about us, dan contact form.
- Modul kategori dan produk diklaim mempunyai CRUD lengkap, termasuk upload gambar.
- Modul laporan keuangan diklaim diagregasi per hari dan menampilkan modal keluar/HPP, omzet, dan laba bersih.
- Halaman POS diklaim mendukung pencarian, filter kategori, input barcode berbasis Enter, kalkulasi cart, pembatasan stok, serta perhitungan kembalian.

Validasi kondisi tiap poin dari file route, controller, model, migration, Blade, dan JavaScript sebelum menjadikannya dasar perubahan.

## 5. Isu lama yang perlu dijaga

1. **Inkonsistensi `$totalOmset` vs `$totalOmzet`.** Ringkasan lama menyebut error undefined variable dan mengklaim telah memperbaikinya. Periksa nama variabel pada controller dan Blade laporan yang aktif sebelum mengubah apa pun. Jangan mengganti nama secara parsial. Untuk data dashboard yang diminta di bawah, gunakan kontrak `$totalOmset`, `$totalLaba`, `$totalTransaksi`, dan `$stokMenipis`, kecuali repository sudah mempunyai kontrak konsisten yang perlu dipertahankan; jika ada perbedaan, laporkan dulu dan selaraskan controller serta view secara bersamaan.
2. Tombol aksi tabel pernah bertumpuk vertikal. Pertahankan pola flex gap yang cocok dengan UI existing.
3. Empat halaman berikut dilaporkan belum sepenuhnya mengikuti standar UI final:
   - `resources/views/pages/dashboard/index.blade.php`
   - `resources/views/pages/settings/index.blade.php`
   - `resources/views/pages/history/index.blade.php`
   - `resources/views/pages/users/index.blade.php`

## 6. Pekerjaan tersisa dan kriteria selesai

### A. Dashboard backend dan view

- Audit controller/routing/model/transaksi/laporan yang sudah ada terlebih dahulu.
- Lengkapi logika untuk data hari ini: `$totalOmset`, `$totalLaba`, `$totalTransaksi`; dan daftar `$stokMenipis` untuk produk dengan stok `< 10`.
- Gunakan kolom, status transaksi, relasi, dan definisi HPP/laba yang benar-benar ada dalam repository. Reuse definisi laporan keuangan yang sudah tersedia bila konsisten; jangan menebak rumus laba.
- Pastikan filter hari mengikuti timezone aplikasi dan transaksi batal/void tidak ikut dihitung apabila aplikasi memang memiliki konsep status tersebut.
- Pastikan dashboard view memakai nama variabel yang sama persis dengan controller dan memiliki empty state yang sesuai.
- Kriteria selesai: dashboard berjalan, angka sesuai data uji yang diketahui, tidak ada undefined variable, dan test/check yang relevan lulus.

### B. Standarisasi empat halaman UI

File sasaran:

- `resources/views/pages/dashboard/index.blade.php`
- `resources/views/pages/settings/index.blade.php`
- `resources/views/pages/history/index.blade.php`
- `resources/views/pages/users/index.blade.php`

- Bandingkan setiap view dengan halaman Kategori/Produk sebagai referensi visual aktual.
- Samakan header, alert, card, tabel, tombol, action alignment, empty state, dan responsive behavior dengan design system.
- Pertahankan route, method HTTP, CSRF, validation error, data binding, modal, authorization, dan seluruh perilaku yang sedang bekerja.
- Jangan membuat ulang seluruh file dari nol apabila perubahan terlokalisasi sudah cukup. Jangan menghapus fitur hanya demi keseragaman UI.
- Kriteria selesai: empat halaman konsisten dan fitur existing tetap bekerja.

### C. UI/UX cetak struk thermal

- Temukan alur checkout/riwayat dan route yang benar-benar digunakan untuk cetak struk.
- Temukan sumber data Settings serta key/kolom aktual untuk nama toko, alamat, dan pesan penutup.
- Implementasikan atau rapikan `print.blade.php` di lokasi yang sesuai dengan struktur route/view saat ini; jangan mengarang path/route/model baru sebelum audit.
- Dukung layout thermal 58 mm dan 80 mm melalui CSS print yang terkontrol. Periksa margin, wrapping teks, total, item, qty, harga, tanggal, identitas transaksi, dan aturan `@media print`.
- Jangan mengubah mekanisme pencatatan transaksi hanya untuk mengakomodasi tampilan struk.
- Kriteria selesai: data struk terhubung dengan transaksi yang benar, identitas toko berasal dari Settings, tampilan tidak terpotong pada ukuran target, dan tidak ikut mencetak elemen navigasi aplikasi.

### D. Verifikasi checkout dan pengurangan stok

- Telusuri route POS, validasi request, method `store()`/checkout aktual, model transaksi/detail, dan update stok produk.
- Pastikan stok hanya berkurang setelah transaksi valid berhasil diproses, kuantitas tidak dapat melebihi stok, serta kegagalan penyimpanan tidak meninggalkan transaksi/detail/stok dalam kondisi parsial.
- Evaluasi kebutuhan database transaction dan locking berdasarkan pola kode dan risiko concurrency yang ada. Jangan langsung mengubah implementasi sebelum memahami alur dan schema.
- Uji kasus normal, stok tidak cukup, kuantitas invalid, item ganda bila didukung, serta kegagalan penyimpanan jika test setup memungkinkan.
- Kriteria selesai: test membuktikan stok awal, qty yang terjual, dan stok akhir; transaksi serta detail konsisten; dan kegagalan tidak meninggalkan data parsial.
- Jalankan test menggunakan database test/isolated. **Jangan melakukan checkout uji pada data transaksi nyata. Jangan menjalankan `migrate:fresh`, `db:wipe`, truncate, atau operasi destruktif terhadap database aktif.**

## 7. Aturan kerja untuk Codex

- Mulai dengan read-only audit. Laporan AI lama dan dokumen ini adalah petunjuk, bukan pengganti bukti source code.
- Jangan berasumsi tentang versi Laravel, skema/kolom DB, class model, route name, middleware, path view, relasi, status transaksi, atau key Settings. Cari dari repository aktual.
- Baca file yang relevan dengan task saat ini. Prioritaskan pola yang sudah dipakai proyek.
- Jangan mengubah framework, dependency, schema database, autentikasi, authorization, atau arsitektur tanpa alasan kuat dan persetujuan pengguna.
- Jangan menghapus file, deployment, mengubah database aktif, menjalankan migrasi destruktif, commit, push, atau mengubah remote tanpa persetujuan eksplisit.
- Jangan membaca, menyalin, atau menampilkan isi `.env`, credentials, API key, password, atau token. Jika perlu memeriksa konfigurasi database, cukup laporkan nama koneksi/environment tanpa menampilkan kredensial.
- Perubahan harus terfokus, bisa ditinjau dalam diff, dan sesuai Laravel/Bootstrap versi yang dipasang.
- Setelah mengubah kode, jalankan pemeriksaan yang relevan jika aman dan tersedia; laporkan command yang dijalankan, hasil nyata, test yang belum dijalankan, serta risiko/ketidakpastian.
- Jangan mengklaim test lulus jika belum dijalankan.
- Untuk tiap tahap, beri laporan singkat: file diperiksa, temuan, file berubah, alasan, pengujian, dan sisa pekerjaan.

## 8. Urutan pengerjaan yang disarankan

Urutan ini mengutamakan dependency dan keselamatan data, sehingga dashboard backend dikerjakan bersama view dashboard terlebih dahulu.

1. **Baseline/read-only audit:** status Git, instruksi repository, versi Laravel/PHP, routes, controller, models, migrations, view Kategori/Produk, empat view target, alur checkout, settings dan test yang ada. Tidak mengubah file.
2. **Dashboard:** verifikasi definisi angka dari laporan, implementasikan dashboard controller, sambungkan view dashboard, lalu test/check.
3. **Standarisasi UI:** rapikan settings, history, dan users satu halaman atau satu kelompok kecil per perubahan; bandingkan dengan referensi Kategori/Produk.
4. **Struk thermal:** telusuri route dan data transaksi/settings, lalu implementasikan print view dengan CSS 58 mm/80 mm dan verifikasi print.
5. **Checkout dan stok:** buat/benahi test pada database terisolasi, verifikasi atomicity, validasi stok, dan hasil stok akhir.
6. **Final regression:** cek diff, jalankan test suite yang relevan, inspeksi route/view, periksa UI manual, dan buat laporan perubahan.

## 9. Prompt pembuka (jalankan sebelum implementasi)

Salin prompt ini ke Codex setelah membuka root folder project:

> Ambil alih proyek POS skincare berdasarkan `POS_Skincare_Codex_Handoff.md` jika file tersebut tersedia di repository, serta instruksi `AGENTS.md` yang berlaku. Untuk sesi ini lakukan **audit read-only saja**; jangan membuat atau mengubah file, jangan menjalankan migrasi, dan jangan mengubah database.
>
> Periksa status Git dan perubahan yang sudah ada; versi Laravel/PHP dari file dependency; routes terkait dashboard, settings, history, users, POS, laporan, dan print; model, migration, relasi, kolom stok/harga/HPP/status transaksi; controller terkait; view Kategori/Produk sebagai referensi UI; empat view target; konfigurasi Settings; serta test yang tersedia.
>
> Jangan membaca atau menampilkan `.env` atau secret. Jangan berasumsi nama kolom, route, model, status transaksi, atau formula laporan. Periksa dan laporkan ketidaksesuaian `$totalOmset` vs `$totalOmzet` tanpa memperbaikinya dulu.
>
> Berikan hasil dalam bagian: (1) kondisi Git dan file yang sudah berubah, (2) peta file aktual yang relevan, (3) perilaku yang sudah terbukti ada, (4) gap terhadap daftar pekerjaan, (5) risiko dan informasi yang belum pasti, (6) urutan implementasi yang disarankan, dan (7) perintah test/check aman yang tersedia. Setelah audit, berhenti dan tunggu persetujuan saya sebelum implementasi.

## 10. Prompt per tahap

### Tahap 1 — Dashboard

> Berdasarkan audit dan persetujuan saya, kerjakan hanya dashboard: controller yang benar-benar dipakai route dan `resources/views/pages/dashboard/index.blade.php`. Verifikasi formula omzet/laba dari implementasi laporan yang ada dan schema/model aktual. Buat `$totalOmset`, `$totalLaba`, `$totalTransaksi`, serta `$stokMenipis` untuk stok `< 10`, tanpa mengubah behavior modul lain. Gunakan aturan UI proyek. Sebelum mengedit, sebutkan file dan rencana perubahan; kemudian implementasikan, jalankan test/check aman, dan laporkan hasil. Jangan mengubah schema, dependency, `.env`, data aktif, atau file yang tidak relevan.

### Tahap 2 — Standarisasi Settings, History, Users

> Kerjakan hanya tiga view berikut: `resources/views/pages/settings/index.blade.php`, `resources/views/pages/history/index.blade.php`, dan `resources/views/pages/users/index.blade.php`. Bandingkan dengan view Kategori/Produk aktual. Terapkan design system yang telah disepakati pada header, alert, card, tabel, button, alignment, empty state, dan responsive behavior. Pertahankan data binding, route/action, form, CSRF, validation, pagination, modal, dan authorization. Jangan mengubah controller atau model kecuali ada bug yang terbukti dan minta persetujuan saya terlebih dahulu. Setelah selesai, periksa diff dan lakukan validasi yang relevan.

### Tahap 3 — Struk thermal

> Telusuri alur route/controller transaksi dan sumber Settings terlebih dahulu. Berdasarkan temuan aktual, implementasikan/rapikan view cetak struk pada lokasi yang sesuai, dengan dukungan CSS thermal 58 mm dan 80 mm. Ambil nama toko, alamat, dan pesan penutup dari data Settings yang benar-benar digunakan aplikasi. Jangan mengarang route/model/field atau mengubah pencatatan transaksi. Sertakan identitas transaksi, waktu, item, qty, harga, subtotal/total, pembayaran, dan kembalian hanya jika data tersebut memang tersedia dan sesuai model transaksi. Uji print layout dan laporkan batasan yang tidak dapat diverifikasi.

### Tahap 4 — Checkout, konsistensi transaksi, dan stok

> Audit checkout aktual sebelum mengedit: validasi input, transaksi database, penyimpanan header/detail, kalkulasi harga, perubahan stok, serta penanganan kegagalan. Susun test menggunakan database test/isolated dan factory/fixture yang sesuai proyek. Buktikan stok awal, qty, stok akhir, transaksi/detail tersimpan, penolakan stok tidak cukup, dan rollback saat kegagalan jika dapat disimulasikan. Evaluasi race condition berdasarkan implementasi aktual. Jangan menjalankan checkout pada database nyata, jangan menampilkan secret, jangan menjalankan migrasi destruktif, dan jangan mengubah schema kecuali ditemukan kebutuhan yang dibuktikan serta disetujui saya. Jalankan test relevan dan laporkan hasil aktual.

### Tahap 5 — Review akhir

> Tinjau diff semua tahap terhadap requirement dan implementasi awal. Cari bug regression, variable mismatch, route/view yang tidak terhubung, ketidaksesuaian UI, masalah stok/atomicity, dan paparan secret. Jalankan test/check yang aman dan tersedia. Jangan membuat perubahan tambahan yang tidak diminta tanpa melaporkannya terlebih dahulu. Laporkan file berubah, hasil pemeriksaan, test lulus/gagal/tidak dijalankan, serta pekerjaan yang masih tersisa.
