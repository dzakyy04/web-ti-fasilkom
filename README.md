# Website TI UNSRI

Sebuah website jurusan Teknik Informatika Fakultas Ilmu Komputer Universitas Sriwijaya

## Persyaratan

1. Install PHP 8.1 - 8.2
2. Install Composer
3. Install MySQL

## Cara Menjalankan

Clone repositori

    git clone https://github.com/dzakyy04/web-ti-fasilkom.git

Masuk ke folder repositori

    cd web-ti-fasilkom

Install semua dependensi menggunakan Composer

    composer install

Copy file .env.example dan atur konfigurasi yang diperlukan di file .env

    cp .env.example .env

Generate kunci aplikasi baru

    php artisan key:generate

Jalankan migrasi database (**Atur koneksi database di .env sebelum melakukan migrasi**)

    php artisan migrate

Jalankan seeder untuk mengisi database dengan data awal

    php artisan db:seed

Buat symbolic link

    php artisan storage:link

Generate dokumentasi API

    php artisan l5-swagger:generate

Mulai local development server

    php artisan serve

Anda sekarang dapat mengakses server di [http://localhost:8000](http://localhost:8000) dan dokumentasi API di [http://localhost:8000/documentation](http://localhost:8000/documentation)
