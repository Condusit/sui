@ECHO OFF
:loop
call C:\xampp\php\php.exe C:\xampp\htdocs\sui\Entrada\Mix\ExecutorMix.php
TIMEOUT /T 30
goto loop