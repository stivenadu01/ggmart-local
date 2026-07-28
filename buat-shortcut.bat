@echo off
title GLOBAL ICON CACHE CLEANER - GGMART
cls

echo ===================================================
echo   PEMBERSIHAN TOTAL CACHE IKON WINDOWS
echo ===================================================
echo.

@REM set "TARGET=D:\ggmart-local\ggmart.bat"
set "TARGET=D:\ggmart-local\launcher.vbs"
set "ICON=D:\ggmart-local\logo.ico"

echo [1/4] Membuat ulang file shortcut GGMart...
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
    "$WshShell = New-Object -ComObject WScript.Shell; " ^
    "$Shortcut = $WshShell.CreateShortcut(([Environment]::GetFolderPath('Desktop') + '\GGMart.lnk')); " ^
    "$Shortcut.TargetPath = '%TARGET%'; " ^
    "$Shortcut.WorkingDirectory = 'D:\ggmart-local'; " ^
    "$Shortcut.IconLocation = '%ICON%'; " ^
    "$Shortcut.Save()"
echo       [OK] Jaringan jalur ikon diset ke: D:\ggmart-local\logo.ico
echo.

echo [2/4] Mematikan Windows Explorer untuk pembersihan...
taskkill /F /IM explorer.exe >nul 2>&1
timeout /t 2 /nobreak >nul

echo [3/4] Menghapus paksa database IconCache yang korup...
:: Menggunakan atribut /A /F /Q untuk membabat habis file sistem yang tersembunyi
del /A /F /Q "%localappdata%\IconCache.db" >nul 2>&1
del /A /F /Q "%localappdata%\Microsoft\Windows\Explorer\iconcache*" >nul 2>&1

echo [4/4] Membangun ulang sistem grafis Desktop...
start explorer.exe

echo.
echo ===================================================
echo   PROSES MEMBERSIHKAN CACHE SELESAI!
echo ===================================================
echo   Database ikon yang rusak telah dihancurkan.
echo   Windows sekarang dipaksa membaca ulang logo.ico Anda!
echo ===================================================
pause