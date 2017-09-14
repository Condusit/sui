@ECHO OFF
:loop
call C:\xampp\php\php.exe C:\xampp\htdocs\sui\Entrada\Tracker\ExecutorTracker.php
TIMEOUT /T 30
goto loop