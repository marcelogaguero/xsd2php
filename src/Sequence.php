<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 08:51 
 */

namespace src;

require_once "phar://xsd2php.phar/src/Base.php";
require_once "phar://xsd2php.phar/src/GenerateCodePhp.php";

use src\Base;
use src\GenerateCodePhp;

class Sequence extends Base {

    protected $path;
    protected $elements = array();

    function __construct(\SimpleXMLElement $xsd, $path, $config = array()){
        parent::__construct($xsd, $config);
        $this->path = $path;
        $this->getTypes($xsd);
    }

    private function getTypes(\SimpleXMLElement $xsd){
        $elements = $xsd->xpath("element");
        if($elements){
            foreach($elements as $element){
                $name = (string) $element->attributes()->name;
                $type = (string) $element->attributes()->type;
                $this->elements[$name] = $type;
            }
        }
    }

    public function generateCode()
    {
        $code = "";
        foreach($this->elements as $name => $type){
            $generate = new GenerateCodePhp($this->path, $this->config);
            $code .= $generate->generateAttribute($name, "array()", $type, 'array');
        }

        return $code;
    }
}