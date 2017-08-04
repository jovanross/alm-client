<?php

namespace AlmClient;

class AlmEntityParametersManager
{

    private static $Instance;

    protected $lists;

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function __construct(){}

    public function SetLists($lists)
    {
        $this->lists = $lists;

        return $this->lists;
    }

    public function GetLists()
    {
        if (null === $this->lists) {

            $this->lists = $this->GetRefreshedLists();

        }

        return $this->lists;
    }

    protected function GetListValues($listId)
    {
        $listItems = array();

        foreach ($this->GetLists() as $list) {

            if ($list->Id == $listId) {

                foreach ($list->Items[0] as $listItem) {

                    array_push($listItems, (string)$listItem->attributes()->value);

                }

            }

        }

        return $listItems;
    }

    public function GetEntityTypeFields($entityType, $onlyRequiredFields = false, $asXml = false)
    {

        \AlmClient\AlmCurl::GetInstance()->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityFieldsUrl($entityType, $onlyRequiredFields));
        $xml = simplexml_load_string(\AlmClient\AlmCurl::GetInstance()->GetResult());

        if (false === $xml) {

            throw new \Exception('Cannot get entity required fields, server returned incorrect XML');

        }

        if ($asXml) {

            return $xml->asXML();

        }

        $fields = array();

        foreach ($xml as $field) {
            $fieldData = array();

            $fieldData['label'] = (string)$field->attributes()->Label;
            $fieldData['editable'] = (string)$field->Editable[0] == "true" ? true : false;

            if (property_exists($field, 'List-Id')) {
                $fieldData['list'] = $this->GetListValues((string)$field->{'List-Id'});
            }

            $fields[(string)$field->attributes()->Name] = $fieldData;
        }

        return $fields;
    }

    public function GetEntityEditableParameters(AlmEntity $entity)
    {
        $arr = array();

        foreach ($this->GetEntityTypeFields($entity->GetType()) as $fieldName => $fieldData) {

            if ($fieldData['editable']) {

                array_push($arr, $fieldName);

            }

        }

        return $arr;
    }

    public function GetRefreshedLists()
    {
        \AlmClient\AlmCurl::GetInstance()->Execute(\AlmClient\AlmRoutes::GetInstance()->GetListsUrl());
        $xml = simplexml_load_string(\AlmClient\AlmCurl::GetInstance()->GetResult());

        if (false === $xml) {

            throw new \Exception('Cannot get lists data');

        }

        return $xml;
    }

}
