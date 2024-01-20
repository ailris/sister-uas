# README: Program ice.php

## Deskripsi
Program `ice.php` adalah sebuah aplikasi web yang memanfaatkan XAMPP untuk menjalankan server Apache dan MySQL. Aplikasi ini memungkinkan pengguna untuk mengelola data (CREATE, READ, UPDATE, DELETE) tentang berbagai rasa es krim.

## Cara Penggunaan

### Langkah 1: Install XAMPP
Pertama, Anda perlu menginstal XAMPP pada komputer Anda. Setelah instalasi selesai, nyalakan Apache dan MySQL melalui kontrol panel XAMPP.

### Langkah 2: Buat Database
Buka phpMyAdmin melalui browser Anda dan buat database baru dengan nama `ice`. Di dalam database ini, buat tabel `flavors` dengan struktur sebagai berikut:

```sql
CREATE DATABASE IF NOT EXISTS ice;
USE ice;

CREATE TABLE IF NOT EXISTS flavors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ice_name VARCHAR(255) NOT NULL,
    ice_type VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL
) ENGINE=InnoDB;
```

### Langkah 3: Download dan Ekstrak File Program
Anda dapat mendownload file program dari [sini](https://github.com/ailris/sister-uas). Setelah file berhasil diunduh, ekstrak file tersebut ke direktori `htdocs` pada folder instalasi XAMPP Anda.

### Langkah 4: Jalankan Program
Buka browser Anda dan jalankan program dengan mengetikkan `http://localhost/sister-uas/ice.php` pada address bar.

### Langkah 5: Mengecek JSON
Untuk mengecek JSON, Anda dapat menggunakan F12 atau developer tool pada browser Anda. Tekan pada bagian console, atau network untuk melihat respon API pada JSON (GET, POST).

## Catatan
Pastikan server Apache dan MySQL Anda berjalan dengan baik sebelum menjalankan program ini. Selamat mencoba!
