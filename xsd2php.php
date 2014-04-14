<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 08:54 
 */
require_once "phar://xsd2php.phar/src/exceptions/Xsd2PhpException.php";
require_once "phar://xsd2php.phar/src/Element.php";
require_once "phar://xsd2php.phar/src/SimpleType.php";
require_once "phar://xsd2php.phar/src/ComplexType.php";

use src\exceptions\Xsd2PhpException;
use src\Element;
use src\SimpleType;
use src\ComplexType;

class xsd2php {

    private $config;
    private $simple;
    private $complex;
    private $element;

    function __construct($config){
        $this->config = $config;
    }

    public function compiler($xsd_path, $class_path) {

        if(file_exists($xsd_path)) {
            $ext = pathinfo($xsd_path, PATHINFO_EXTENSION);
            if($ext === 'xsd'){
                $xsd = simplexml_load_file($xsd_path);
                $str_xsd = str_replace("xs:", "", $xsd->asXML());
                $xsd = new SimpleXMLElement($str_xsd);

                if(!$this->createFolder($class_path)){
                    throw new Xsd2PhpException("No se pudo crear la carpeta de destino ".$class_path);
                }

                $this->includes($xsd, $class_path, dirname($xsd_path));

                $this->simple   = $this->generateSimpleTypes($xsd, $class_path);
                $this->complex  = $this->generateComplexTypes($xsd, $class_path);
                $this->elements = $this->generateElements($xsd, $class_path);

                $this->generateCode();
            } else {
                throw new Xsd2PhpException("Extension no válida del archivo ".$xsd_path);
            }
        } else {
            throw new Xsd2PhpException("No existe el esquema ".$xsd_path);
        }
    }

    protected function generateSimpleTypes(\SimpleXMLElement $xsd, $class_path){
        $elements = array();
        $elements_tags = $xsd->xpath('simpleType');
        if($elements_tags){
            foreach($elements_tags as $element_tag){
                $elements[] = new SimpleType($element_tag, $class_path, $this->config);
            }
        }
        return $elements;
    }

    protected function generateComplexTypes(\SimpleXMLElement $xsd, $class_path){
        $elements = array();
        $elements_tags = $xsd->xpath('complexType');
        if($elements_tags){
            foreach($elements_tags as $element_tag){
                $elements[] = new ComplexType($element_tag, $class_path, $this->config);
            }
        }
        return $elements;
    }

    protected function generateElements(\SimpleXMLElement $xsd, $class_path){
        $elements = $xsd->xpath('element');
        if($elements){
            foreach($elements as $element_tag){
                $element = new Element($element_tag, $class_path, $this->config);
            }
        }
        return $elements;
    }

    protected function createFolder ($pathname) {
        if(is_dir($pathname)) return true;
        return mkdir ($pathname, 0777, true);
    }

    protected function includes(\SimpleXMLElement $xsd, $class_path, $root) {
        $includes = $xsd->xpath('include');
        if($includes){
            foreach($includes as $include){
                $file = $root . "/" . (string) $include->attributes()->schemaLocation;
                $this->compiler($file, $class_path);
            }
        }
    }

    protected function generateCode(){
        foreach($this->simple as $simple){
            $simple->generateCode();
        }

        foreach($this->complex as $complex){
            $complex->generateCode();
        }
    }
} 