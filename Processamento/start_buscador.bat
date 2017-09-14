@ECHO OFF
:loop
call C:\xampp\php\php.exe C:\xampp\htdocs\sui\Processamento\Buscador.php
TIMEOUT /T 30
goto loop