<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 08:50 
 */

namespace src;

require_once "phar://xsd2php.phar/src/Base.php";
require_once "phar://xsd2php.phar/src/GenerateCodePhp.php";

use src\Base;
use src\GenerateCodePhp;

class SimpleType extends Base {
    private $base;
    private $path;

    function __construct(\SimpleXMLElement $element, $path, $config = array()){
        $this->base   = (string) current($element->xpath('restriction'))->attributes()->base;
        $this->path   = $path;
        parent::__construct($element, $config);
    }

    public function generateCode()
    {
        $generate = new GenerateCodePhp($this->path, $this->config);
        return $generate->generateAttribute($this->getName(), 'null');
    }
}