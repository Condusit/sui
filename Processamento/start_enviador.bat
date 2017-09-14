@ECHO OFF
:loop
call C:\xampp\php\php.exe C:\xampp\htdocs\sui\Processamento\Enviador.php
TIMEOUT /T 30
goto loop