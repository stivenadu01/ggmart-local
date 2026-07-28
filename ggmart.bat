@echo off
title GGMART DESKTOP APP (PORTABLE NATIVE)
cls

echo ===================================================
echo   MEMULAI APLIKASI GGMART 
echo ===================================================
echo.

:: 1. Pindah ke folder proyek GGMart
cd /d D:\ggmart-local

:: 2. Menyalakan Database MySQL Bawaan Laragon secara Senyap
echo [1/3] Menyalakan database (Port 3306)...
:: Mengarahkan datadir langsung ke folder data internal proyek
start /b .\server\mysql\bin\mysqld.exe --defaults-file="D:\ggmart-local\server\mysql\my.ini"

:: 3. Menyalakan Server PHP Built-in (Misal kita set di Port 8080 agar anti-bentrok)
echo [2/3] Menyalakan server GGMart (Port 80)...
start /b .\server\php\php.exe -S localhost:80 -t .\ggmart-src\public

:: Jeda 2 detik untuk memastikan service Windows sudah siap menerima koneksi
timeout /t 2 /nobreak >nul

:: 4. Buka Browser Google Chrome dalam Mode Aplikasi Desktop
echo [3/3] Membuka GGMart dalam Mode Jendela Aplikasi...
echo -----------------------------------------------------------
echo PENTING UNTUK PEGAWAI KASIR:
echo Jika toko sudah tutup, CUKUP TUTUP JENDELA CHROME (Klik X), 
echo maka server dan database otomatis mati.
echo -----------------------------------------------------------
echo.

:: Mengunci jendela Chrome Kasir dengan profil terisolasi
start /wait chrome --app=http://localhost --user-data-dir="D:\ggmart-local\chrome-profile"

:: ===================================================
:: LOGIKA OTOMATIS SAAT TOMBOL X CHROME DIKLIK
:: ===================================================
echo.
echo ===================================================
echo   DETEKSI: JENDELA KASIR DITUTUP (TOKO TUTUP)
echo   SEDANG MEMBERSIHKAN RAM SECARA TOTAL...
echo ===================================================
echo.

echo Menghentikan proses background database dan server...
taskkill /F /IM mysqld.exe >nul 2>&1
taskkill /F /IM php.exe >nul 2>&1

echo [SUKSES]
timeout /t 3 /nobreak >nul
exit