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

            array_walk_recursive($array, array ($xml, 'addChild'));

            return $xml->asXML();

        } catch (\Exception $e) {

            throw new \Exception('ALMXMLMessage : ' . $e->getMessage());

        }

    }

    public function DeconstructMessage($xml = '')
    {

        try {

            if(!$xml) return array();

            $message = new \SimpleXMLElement($xml);

            $array = array();

            foreach($message as $key => $val)
            {
                $array[$key] = $val;
            }

            return $array;

        } catch (\Exception $e) {

            throw new \Exception('ALMXMLMessage : ' . $e->getMessage());

        }

    }

}