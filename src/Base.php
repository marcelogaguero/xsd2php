<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 10/04/14 14:30 
 */

namespace src;

abstract class Base {
    protected $name;
    protected $comments;
    protected $config;

    function __construct(\SimpleXMLElement $element_tag, $config = array()){
        $this->name = (string) $element_tag->attributes()->name;
        $this->comments = $this->getAnnotations($element_tag);
        $this->config = $config;
    }

    protected function getAnnotations(\SimpleXMLElement $xsd){
        $comments = array();
        $annotations = $xsd->xpath("annotation");
        if($annotations){
            foreach($annotations as $annotation){
                $comments[] = (string) current($annotation->xpath("documentation"));
            }
        }
        return $comments;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    abstract public function generateCode();
} 