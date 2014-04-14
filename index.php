<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 09:32 
 */

    require_once "phar://xsd2php.phar/xsd2php.php";

    // $argv = array();
    if(count($argv) == 3){

        try {
            $config = parse_ini_file(str_replace('xsd2php.phar',"",str_replace('phar://',"",__DIR__))."config.ini");

            $xsd2php = new xsd2php($config);
            $xsd2php->compiler($argv[1], $argv[2]);
        } catch(\Exception $e){
            echo $e->getTraceAsString() . "\n";
        }
    } else {
        echo "\nParametros inválidos \n";
    }

    echo "\nFIN\n";


