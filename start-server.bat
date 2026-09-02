@echo off
title Jurnal Mengajar Guru

cd /d "%~dp0"

echo ============================================
echo    JURNAL MENGAJAR SMKN 3 Marabahan
echo ============================================
echo.

:: Jalankan server CI4
start "CI4 Server" /MIN "C:\xampp\php\php.exe" spark serve --host=localhost --port=8080

:: Tunggu server siap
timeout /t 4 /nobreak >nul

:: Cek apakah aplikasi sudah diinstall
if exist ".installed" (
    echo Aplikasi sudah diinstall...
    start "" http://localhost:8080
) else (
    echo Aplikasi belum diinstall...
    start "" http://localhost:8080/install.php
)

exit