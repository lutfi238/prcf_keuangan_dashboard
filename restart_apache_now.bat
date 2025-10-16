@echo off
cls
echo ============================================
echo    RESTARTING APACHE FOR MAINTENANCE
echo ============================================
echo.

echo [1/3] Stopping Apache...
net stop Apache2.4 2>nul
if errorlevel 1 (
    echo Apache already stopped or run XAMPP Control Panel manually
) else (
    echo Apache stopped successfully!
)

echo.
echo [2/3] Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo.
echo [3/3] Starting Apache...
net start Apache2.4 2>nul
if errorlevel 1 (
    echo Failed! Please use XAMPP Control Panel:
    echo 1. Click STOP on Apache
    echo 2. Wait 2 seconds
    echo 3. Click START on Apache
) else (
    echo Apache started successfully!
)

echo.
echo ============================================
echo    DONE! Apache Restarted
echo ============================================
echo.
echo Next steps:
echo 1. Clear browser cache (Ctrl + Shift + R)
echo 2. Logout: https://your-ngrok-url/logout.php
echo 3. Test: https://your-ngrok-url/login.php
echo.
pause

