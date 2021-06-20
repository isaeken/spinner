@echo off
for /F "usebackq tokens=2* delims=: " %%W in (`mode con ^| findstr Columns`) do set CONSOLE_WIDTH=%%W
echo %CONSOLE_WIDTH%
