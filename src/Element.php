<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 08:39 
 */

namespace src;

require_once "phar://xsd2php.phar/src/Base.php";
require_once "phar://xsd2php.phar/src/GenerateCodePhp.php";

use src\Base;
use src\GenerateCodePhp;

class Element extends Base {

    private $abstract;

    function __construct(\SimpleXMLElement $element_tag, $path, $config = array()){

        $this->path = $path;
        parent::__construct($element_tag);

        $this->abstract = (bool) $element_tag->attributes()->abstract;
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    public function generateCode()
    {
        $code = "";

        /** @var \src\Sequence $sequence */
        foreach($this->sequences as $sequence){
            $code .= $sequence->generateCode();
        }

        /** @var \src\Attribute $sequence */
        foreach($this->attributes as $attribute){
            $code .= $attribute->generateCode()."\n";
        }

        $generate = new GenerateCodePhp($this->path);
        $generate->generateClass($this->name, $code);

        return $code;
    }
}
