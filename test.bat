@echo off
setlocal

title Sosialin — 1-Click Dev Runner

REM Pindah ke folder lokasi skrip (harap simpan file ini di root Laravel)
cd /d "%~dp0"

REM === OPSIONAL: set path PHP jika tidak ada di PATH ===
REM Contoh: uncomment salah satu baris di bawah jika perlu
REM set "PHP_BIN=C:\xampp\php\php.exe"
REM set "PHP_BIN=H:\Data\xampp\php\php.exe"
REM Jika PHP sudah ada di PATH, biarkan default:
set "PHP_BIN=php"

REM === Jalankan tiap proses di jendela terpisah ===
start "Laravel Serve (http://127.0.0.1:8000)" cmd /k "%PHP_BIN% artisan serve"
start "Vite - npm run dev"                      cmd /k "npm run dev"
start "Scheduler (php artisan schedule:work)"  cmd /k "%PHP_BIN% artisan schedule:work"
start "Queue Worker (php artisan queue:work)"  cmd /k "%PHP_BIN% artisan queue:work"

echo Semua proses sudah dijalankan di jendela terpisah.
timeout /t 2 >nul