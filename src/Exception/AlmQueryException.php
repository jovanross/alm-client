<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 11.02.2016
 * Time: 21:46
 */

namespace StepanSib\AlmClient\Exception;

class AlmQueryException extends AlmException
{

    public function setMessage($message)
    {
        $this->message = 'Query error: '.$message;
    }

}
