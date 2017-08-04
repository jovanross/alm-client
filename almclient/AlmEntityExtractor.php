<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 11.02.2016
 * Time: 16:33
 */

namespace AlmClient;

class AlmEntityExtractor
{

    private static $Instance;

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function Pack(AlmEntity $entity, array $editableParameters = array())
    {

        try {

            $xml = new \SimpleXMLElement('<Entity></Entity>');
            $xml->addAttribute('Type', $entity->getType());
            $xmlFields = $xml->addChild('Fields');

            $parameters = $entity->GetParameters();

            foreach ($parameters as $field => $value) {

                $isParameterPackable = true;

                if (count($editableParameters) > 0 && !in_array($field, $editableParameters)) {

                    $isParameterPackable = false;

                }

                if ($isParameterPackable) {

                    $xmlField = $xmlFields->addChild('Field');
                    $xmlField->addAttribute('Name', $field);
                    $xmlField->addChild('Value', $value);

                }
            }

            return $xml;

        } catch( \Exception $e){

            throw new \Exception($e->getMessage());

        }

    }

    public function Extract(\SimpleXMLElement $entityXml)
    {

        try {

            $entity = new \AlmClient\AlmEntity($entityXml->attributes()->Type);

            $entityXml = $entityXml->Fields[0];
            foreach ($entityXml->Field as $field) {

                if (trim((string)$field->Value[0]) !== '') {

                    $entity->SetParameter((string)$field->attributes()->Name, $field->Value[0], false);

                }

            }

            return $entity;

        } catch( \Exception $e){

            throw new \Exception($e->getMessage());

        }

    }

}
