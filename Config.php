<?php
spl_autoload_register(function ($class) {
    $thispath = explode('\\', str_replace('/','\\', dirname("C:\\xampp\\htdocs\\sui\\".$class)));
    $rootpath = explode('\\', str_replace('/','\\', dirname($_SERVER["SCRIPT_FILENAME"])));
    $relpath = array();
    $dotted = 0;
    for ($i = 0; $i < count($rootpath); $i++) {
        if ($i >= count($thispath)) {
            $dotted++;
        }
        elseif ($thispath[$i] != $rootpath[$i]) {
            $relpath[] = $thispath[$i]; 
            $dotted++;
        }
    }
    $subCaminho = str_repeat('../', $dotted) . implode('/', array_merge($relpath, array_slice($thispath, count($rootpath))));
    
    $partes = explode("\\", $class);
    $classe = $partes[count($partes)-1];
    
    require str_replace('\\', '/', $subCaminho.($subCaminho != ""?"//":"").$classe . ".php");
});