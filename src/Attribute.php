<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 08:40 
 */

namespace src;

require_once "phar://xsd2php.phar/src/Base.php";
require_once "phar://xsd2php.phar/src/GenerateCodePhp.php";

use src\Base;
use src\GenerateCodePhp;

class Attribute extends Base {

    protected $type;

    function __construct(\SimpleXMLElement $xsd, $path, $config = array()){
        parent::__construct($xsd, $config);
        $this->type = (string) $xsd->attributes()->type;
    }

    protected function isBaseDate($type){
        switch ($type) {
            case 'int':
            case 'string':
            case 'decimal':
            case 'integer':
            case 'boolean':
            case 'date':
            case 'time':
                return true;
            default:
                return false;
        }
    }

    public function generateCode()
    {
        $generate = new GenerateCodePhp($this->config);
        return $generate->generateAttribute($this->name, "null", $this->type, ($this->isBaseDate($this->type)) ? "" : "\\std2php\\" . $this->type);
    }
}