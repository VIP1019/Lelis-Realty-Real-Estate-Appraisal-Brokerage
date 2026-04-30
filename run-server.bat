@echo off
echo Starting Lelis Realty PHP Server...
echo.
echo NOTE: Make sure your MySQL database is running and configured in api/config.php
echo.
echo Serving from 'public' directory at http://localhost:8000
echo Close this window to stop the server.
echo.
start http://localhost:8000
php -S localhost:8000 -t public
