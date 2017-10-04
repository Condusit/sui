@ECHO OFF
:loop
call C:\xampp\php\php.exe C:\xampp\htdocs\sui\Entrada\Sighra\Saida.php
TIMEOUT /T 10
goto loop