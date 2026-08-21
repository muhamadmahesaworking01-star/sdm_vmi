@echo off
setlocal
powershell -ExecutionPolicy Bypass -File "%~dp0setup-project.ps1" %*
endlocal
