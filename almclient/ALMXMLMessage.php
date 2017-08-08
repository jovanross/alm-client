<?php
/**
 * Created by PhpStorm.
 * User: jross0
 * Date: 8/7/17
 * Time: 6:29 PM
 */

namespace AlmClient;

class ALMXMLMessage
{

    private static $Instance;

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function __construct(){}

    public function ConstructMessage($root = 'root', $array = array())
    {

        try {

            if(count($array) === 0) return '';

            $xml = new \SimpleXMLElement('<'.$root.'/>');

            $this->ConstructConverter($array, $xml);

            $lines = explode("\n", $xml->asXML());

            $string = '';

            for ($i = 0; $i < count($lines); $i++ ){

                if($i !== 0) $string .= $lines[$i];

            }

            return $string;

        } catch (\Exception $e) {

            throw new \Exception('ALMXMLMessage : ' . $e->getMessage());

        }

    }

    public function ConstructConverter( $array, \SimpleXMLElement &$xml ) {

        foreach( $array as $key => $value ) {

            if(is_numeric($key)){

                $key = 'item'.$key;

            }

            if( is_array($value) ) {

                $node = $xml->addChild($key);

                $this->ConstructConverter($value, $node);

            } else {

                $xml->addChild($key,$value);

            }
        }

    }

    public function DeconstructMessage($xml = '')
    {

        try {

            $array = array();

            if($xml !== '') {

                $this->XMLParse(new \SimpleXMLIterator($xml), $array);

            }

            return $array;

        } catch (\Exception $e) {

            throw new \Exception('ALMXMLMessage DeconstructMessage: ' . $e->getMessage());

        }

    }

    public function XMLParse(\SimpleXMLIterator $sxi, &$array = array()) {

        $counter = 0;
        for($sxi->rewind(); $sxi->valid(); $sxi->next() ) {

            /*if(!array_key_exists($sxi->key(),$array[$sxi->getName()])){

                $array[$sxi->getName()][] = array($sxi->key() => array());

            }*/

            if(!array_key_exists($sxi->key(),$array)){

                $array[$sxi->key()] = array();

            }

            $array[$sxi->key()][$counter] = array();

            if($sxi->current()->attributes()){

                $attr = array();
                foreach($sxi->current()->attributes() as $key => $val){

                    $attr[$key] = (string) $val;

                }

                $array[$sxi->key()][$counter]['@Attr'][] = $attr;

            }

            if((string)$sxi->current() !== ''){

                $array[$sxi->key()][$counter]['@Value'] = (string)$sxi->current();

            }

            if($sxi->hasChildren()){

                $this->XMLParse($sxi->getChildren(), $array[$sxi->key()][$counter]);

            }

            $counter++;

        }

    }

}