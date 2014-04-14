<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 08:50 
 */

namespace src;

require_once "phar://xsd2php.phar/src/Base.php";
require_once "phar://xsd2php.phar/src/Sequence.php";
require_once "phar://xsd2php.phar/src/GenerateCodePhp.php";
require_once "phar://xsd2php.phar/src/Attribute.php";

use src\Base;
use src\Sequence;
use src\GenerateCodePhp;
use src\Attribute;

class ComplexType extends Base {

    private $attributes = array();
    private $sequences = array();
    private $path;

    function __construct(\SimpleXMLElement $element_tag, $path, $config = array()){
        $this->path = $path;
        $this->generateAttributes($element_tag);
        $this->generateSecuences($element_tag);
        parent::__construct($element_tag, $config);
    }

    protected function generateSecuences(\SimpleXMLElement $xsd){
        $elements = array();
        $sequences = $xsd->xpath("sequence");
        if($sequences){
            foreach($sequences as $sequence){
                $elements[] = new Sequence($sequence, $this->path, $this->config);
            }
        }
        $this->sequences = $elements;
    }

    protected function generateAttributes(\SimpleXMLElement $xsd){
        $elements = array();
        $attributes = $xsd->xpath("attribute");
        if($attributes){
            foreach($attributes as $attribute){
                $elements[] = new Attribute($attribute, $this->path, $this->config);
            }
        }
        $this->attributes = $elements;
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

        $generate = new GenerateCodePhp($this->path, $this->config);
        $generate->generateClass($this->name, $code);

        return $code;
    }
}