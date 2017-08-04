<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 10.02.2016
 * Time: 21:38
 */

namespace AlmClient;

class AlmEntityManager
{

    private static $Instance;

    const HYDRATION_ENTITY = 'array';
    const HYDRATION_NONE = 'none';
    const ENTITY_TYPE_TEST = 'test';
    const ENTITY_TYPE_REQUIREMENT = 'requirement';
    const ENTITY_TYPE_RESOURCE = 'resource';
    const ENTITY_TYPE_DEFECT = 'defect';

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function __construct(){}

    protected function PluralizeEntityType($entityType)
    {
        $entity =  new \AlmClient\AlmEntity($entityType);
        return $entity->GetTypePluralized();
    }

    public function GetOneBy($entityType, array $criteria)
    {
        $result = $this->GetBy($entityType, $criteria, self::HYDRATION_ENTITY);
        return $result[0];
    }

    public function GetBy($entityType, array $criteria, $hydration = self::HYDRATION_ENTITY)
    {
        $criteriaProcessed = array();

        if (count($criteria) == 0) {
            throw new \Exception('AlmEntityManager: Criteria array cannot be empty');
        }

        foreach ($criteria as $key => $value) {
            array_push($criteriaProcessed, $key . '[' . $value . ']');
        }

        $url = \AlmClient\AlmRoutes::GetInstance()->GetEntityUrl($this->PluralizeEntityType($entityType)) . '?query={' . implode(';', $criteriaProcessed) . '}';
        $resultRaw = \AlmClient\AlmCurl::GetInstance()->Execute($url)->GetResult();

        switch ($hydration) {
            case self::HYDRATION_ENTITY:
                $xml = simplexml_load_string($resultRaw);

                $resultArray = array();
                foreach ($xml->Entity as $entity) {
                    array_push($resultArray, \AlmClient\AlmEntityExtractor::GetInstance()->Extract($entity));
                }

                return $resultArray;
                break;
            case self::HYDRATION_NONE:
                return $resultRaw;
                break;
        }

        throw new \Exception('AlmEntityManager: Incorrect hydration mode specified');

    }

    public function Delete(AlmEntity $entity)
    {
        \AlmClient\AlmCurl::GetInstance()->SetHeaders(array('DELETE /HTTP/1.1'))
            ->SetDelete()
            ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityUrl($entity->GetTypePluralized(), $entity->id));
    }

    public function Save(AlmEntity $entity)
    {

        $headers = array(
            'Accept: application/xml',
            'Content-Type: application/xml',
        );

        if ($entity->IsNew()) {
            $entityXml = \AlmClient\AlmEntityExtractor::GetInstance()->Pack($entity);

            array_push($headers, 'POST /HTTP/1.1');

            \AlmClient\AlmCurl::GetInstance()->SetHeaders($headers)
                ->SetPost($entityXml->asXML())
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityUrl($entity->GetTypePluralized()));

            $xml = simplexml_load_string(\AlmClient\AlmCurl::GetInstance()->GetResult());

        } else {

            $entityXml = \AlmClient\AlmEntityExtractor::GetInstance()->Pack($entity, \AlmClient\AlmEntityParametersManager::GetInstance()->GetEntityEditableParameters($entity));

            if (\AlmClient\AlmEntityLocker::GetInstance()->isEntityLocked($entity)) {

                if (!\AlmClient\AlmEntityLocker::GetInstance()->IsEntityLockedByMe($entity)) {

                    throw new \Exception('AlmEntityManager: Entity is locked by someone');

                }

            } else {

                \AlmClient\AlmEntityLocker::GetInstance()->LockEntity($entity);

            }

            if ($this->IsEntityVersioning($entity)) {

                \AlmClient\AlmEntityLocker::GetInstance()->CheckOutEntity($entity);

            }

            array_push($headers, 'PUT /HTTP/1.1');

            \AlmClient\AlmCurl::GetInstance()->SetHeaders($headers)
                ->SetPut($entityXml->asXML())
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityUrl($entity->GetTypePluralized(), $entity->id));

            $xml = simplexml_load_string(\AlmClient\AlmCurl::GetInstance()->GetResult());

            if ($this->IsEntityVersioning($entity)) {

                \AlmClient\AlmEntityLocker::GetInstance()->CheckInEntity($entity);

            }

            \AlmClient\AlmEntityLocker::GetInstance()->UnlockEntity($entity);

        }

        return \AlmClient\AlmEntityExtractor::GetInstance()->Extract($xml);

    }

    public function IsEntityVersioning(AlmEntity $entity)
    {
        if ($entity->GetType() == AlmEntityManager::ENTITY_TYPE_TEST
            || $entity->GetType() == AlmEntityManager::ENTITY_TYPE_REQUIREMENT
            || $entity->GetType() == AlmEntityManager::ENTITY_TYPE_RESOURCE
        ) {

            return true;

        }

        return false;
    }


}
