@echo off
for /F "usebackq tokens=2* delims=: " %%W in (`mode con ^| findstr Columns`) do set CONSOLE_COLUMNS=%%W
for /F "usebackq tokens=2* delims=: " %%W in (`mode con ^| findstr Lines`) do set CONSOLE_ROWS=%%W
echo {"columns": "%CONSOLE_COLUMNS%", "rows": "%CONSOLE_ROWS%"}
