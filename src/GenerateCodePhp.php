<?php
/**
 * Created by Nemogroup.
 * @author: Marcelo Agüero <marcelo.aguero@nemogroup.net>
 * @since: 11/04/14 07:31 
 */

namespace src;

use src\exceptions\Xsd2PhpException;

class GenerateCodePhp {

    private $path;
    private $config;

    function __construct($path = false, $config = array()){
        $this->path = $path;
        $this->config = $config;
    }

    public function generateFile($fileName, $php="// Código php"){
        $comment = $this->generateComment();
        $code = "<?php \n";
        $code .= "{$comment}\n\n";
        $code .= "{$php}\n";
        $code .= "?>";

        if(file_exists($this->path."/".$fileName.".php")){
            unlink($this->path."/".$fileName.".php");
        }

        $fh = fopen($this->path."/".$fileName.".php", 'a');
        fwrite($fh, $code);
        fclose($fh);

        return true;
    }

    public function generateComment(){
        $created = (isset($this->config['created'])) ? $this->config['created'] : "xsd2php";
        $author = (isset($this->config['author'])) ? $this->config['author'] : "Marcelo Agüero <marcelo.aguero@nemogroup.net>";
        $code = "/**\n";
        $code .= " * Created by {$created}.\n";
        $code .= " * @author {$author}\n";
        $code .= " * @since ".date("d/m/Y H:i")."\n";
        $code .= " */\n";
        return $code;
    }

    public function generateClass($name, $php="// Código php", $comment = ""){
        $namespace = (isset($this->config['namespace'])) ? $this->config['namespace'] : "xsd2php";
        $package   = (isset($this->config['package'])) ? $this->config['package'] : "xsd2php";
        $extends   = (isset($this->config['extends']) && !empty($this->config['extends'])) ? 'extends '.$this->config['extends'] : "";

        $className = ucfirst($this->camelCase($name));
        $code  = "namespace \\{$namespace};\n\n";
        $code .= "/** {$comment} \n";
        $code .= " * @package {$package}\n";
        $code .= " */\n";
        $code .= "class {$className} {$extends} {\n";
        $code .= "{$php}\n";
        $code .= "}";

        $exit = $this->generateFile($className, $code);

        return $exit;
    }

    public function generateAttribute($name, $value, $comment = "", $type = ""){

        if(empty($name)) throw new Xsd2PhpException("Nombre de atributo inválido");
        $scope = (isset($this->config['scope'])) ? $this->config['scope'] : 'protected';
        $code  = "\n\t{$scope} \$".lcfirst($name)." = {$value}; // {$comment}\n\n";

        $code .= "\tpublic function get".ucfirst($name)."() {\n";
        $code .= "\t\t return \$this->".lcfirst($name).";\n";
        $code .= "\t}\n\n";

        $code .= "\tpublic function set".ucfirst($name)."(".$type." \$value) {\n";
        $code .= "\t\t \$this->".lcfirst($name)." = \$value;\n";
        $code .= "\t}\n";

        return $code;
    }

    public function generateConst($name, $value, $comment=""){
        $name = strtoupper($name);
        $code = "\t{$name} = '{$value}'; // {$comment}\n";
        return $code;
    }

    private function camelCase($str, $exclude = array())
    {
        $str = str_replace("_", "", $str);
        $str = str_replace("-", "", $str);
        // replace accents by equivalent non-accents
        $str = self::replaceAccents($str);
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $exclude) . ']+/i', ' ', $str);
        // uppercase the first character of each word
        $str = ucwords(trim($str));
        return lcfirst(str_replace(" ", "", $str));
    }

    private function replaceAccents($str) {
        $search = explode(",",
            "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
        $replace = explode(",",
            "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
        return str_replace($search, $replace, $str);
    }
} 