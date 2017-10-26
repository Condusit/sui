@ECHO OFF

echo Iniciando SUI...
TIMEOUT /T 5 /NOBREAK >nul

cls
echo Iniciando SUI...0.00%%
TIMEOUT /T 1 /NOBREAK >nul



rem Entrada---------------------

    rem Sighra########################################################################################
        cd C:\xampp\htdocs\sui\Entrada\Sighra\

        cls
        Start "IntegracaoSighra-Entrada" /min C:\xampp\htdocs\sui\Entrada\Sighra\Entrada.bat
        echo Iniciando SUI...12.50%%
        TIMEOUT /T 1 /NOBREAK >nul

        cls
        Start "IntegracaoSighra-Processamento" /min C:\xampp\htdocs\sui\Entrada\Sighra\Processamento.bat
        echo Iniciando SUI...25.00%%
        TIMEOUT /T 1 /NOBREAK >nul

        cls
        Start "IntegracaoSighra-Saida" /min C:\xampp\htdocs\sui\Entrada\Sighra\Saida.bat
        echo Iniciando SUI...37.50%%
        TIMEOUT /T 1 /NOBREAK >nul
    rem Sighra########################################################################################


    rem Mix###########################################################################################
        cls
        cd C:\xampp\htdocs\sui\Entrada\Mix\
        Start "IntegracaoMix" /min C:\xampp\htdocs\sui\Entrada\Mix\start_mix.bat
        echo Iniciando SUI...50.00%%
        TIMEOUT /T 1 /NOBREAK >nul
    rem Mix###########################################################################################


    rem Tracker#######################################################################################
        cls
        cd C:\xampp\htdocs\sui\Entrada\Tracker\
        rem Start "IntegracaoTracker" /min C:\xampp\htdocs\sui\Entrada\Tracker\start_tracker.bat
        echo Iniciando SUI...62.50%%
        TIMEOUT /T 1 /NOBREAK >nul
    rem Tracker#######################################################################################
rem Entrada---------------------


rem Processamento---------------
    cls
    cd C:\xampp\htdocs\sui\Processamento\
    Start "ProcessamentoBuscador" /min C:\xampp\htdocs\sui\Processamento\start_buscador.bat
    echo Iniciando SUI...75.00%%
    TIMEOUT /T 1 /NOBREAK >nul

    cls
    Start "ProcessamentoCruzador" /min C:\xampp\htdocs\sui\Processamento\start_cruzador.bat
    echo Iniciando SUI...87.50%%
    TIMEOUT /T 1 /NOBREAK >nul

    cls
    rem Start "ProcessamentoEnviador" /min C:\xampp\htdocs\sui\Processamento\start_enviador.bat
    echo Iniciando SUI...100%%
    TIMEOUT /T 2 /NOBREAK >nul
rem Processamento---------------

cls
echo "SUI iniciado ..."

pause