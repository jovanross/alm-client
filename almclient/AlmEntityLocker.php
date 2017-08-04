<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 15.02.2016
 * Time: 14:48
 */

namespace AlmClient;

class AlmEntityLocker
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

    public function LockEntity(AlmEntity $entity)
    {
        \AlmClient\AlmCurl::GetInstance()->SetHeaders(array(
            'POST /HTTP/1.1',
            'Content-Type: application/xml',
            'Accept: application/xml'
        ))->SetPost()
        ->CreateCookie()
        ->Execute(\AlmClient\AlmRoutes::GetInstance()->getEntityLockUrl($entity->GetTypePluralized(), $entity->id));
    }

    public function UnlockEntity(AlmEntity $entity)
    {
        \AlmClient\AlmCurl::GetInstance()->SetHeaders(array(
            'DELETE /HTTP/1.1',
            'Content-Type: application/xml'
        ))->setDelete()
        ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityLockUrl($entity->GetTypePluralized(), $entity->id));
    }

    public function GetEntityLockStatus(AlmEntity $entity)
    {
        \AlmClient\AlmCurl::GetInstance()->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityLockUrl($entity->GetTypePluralized(), $entity->id));
        $xml = simplexml_load_string(\AlmClient\AlmCurl::GetInstance()->GetResult());
        return (string)$xml->LockStatus[0] . ' (' . (string)$xml->LockUser[0] . ', ' . (string)$xml->LockedByMe[0] . ')';
    }

    public function CheckOutEntity(AlmEntity $entity)
    {
        \AlmClient\AlmCurl::GetInstance()->SetHeaders(array('POST /HTTP/1.1'))
        ->SetPost()
        ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityLockUrl($entity->GetTypePluralized(), $entity->id));
    }

    public function CheckInEntity(AlmEntity $entity)
    {
        \AlmClient\AlmCurl::GetInstance()->SetHeaders(array('POST /HTTP/1.1'))
        ->SetPost()
        ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetEntityLockUrl($entity->GetTypePluralized(), $entity->id));
    }

    public function IsEntityLocked(AlmEntity $entity)
    {
        if (mb_substr_count($this->GetEntityLockStatus($entity), 'UNLOCKED', 'utf-8') > 0) {

            return false;

        }
        return true;
    }

    public function IsEntityLockedByMe(AlmEntity $entity)
    {
        if (mb_substr_count($this->GetEntityLockStatus($entity), 'LOCKED_BY_ME', 'utf-8') > 0) {

            return true;

        }
        return false;
    }

}
