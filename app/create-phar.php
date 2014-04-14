<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 09:22 
 */
$srcRoot = realpath(__DIR__."/../");
$buildRoot = __DIR__;

function includeFile($dir, $phar, $srcRoot) {

    if ($handle = opendir($srcRoot . $dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if(is_file($srcRoot . $dir . "/" .$entry)){
                    $phar[$dir . "/" .$entry] = file_get_contents($srcRoot . $dir . "/" .$entry);
                } else {
                    includeFile($dir."/".$entry, $phar, $srcRoot);
                }
            }
        }
        closedir($handle);
    }
}

$phar = new Phar($buildRoot . "/xsd2php.phar", FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, "xsd2php.phar");

includeFile("/src", $phar, $srcRoot);

$phar["index.php"] = file_get_contents($srcRoot . "/index.php");
$phar["xsd2php.php"] = file_get_contents($srcRoot . "/xsd2php.php");
// $phar["common.php"] = file_get_contents($srcRoot . "/common.php");

$phar->setStub($phar->createDefaultStub("index.php"));

copy($srcRoot . "/config.ini", $buildRoot . "/config.ini");