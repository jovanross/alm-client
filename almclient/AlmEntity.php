<?php

namespace AlmClient;

class AlmEntity
{

    private static $Instance;

    protected $parameters;

    protected $parametersChanged;

    protected $type;

    public static function GetInstance($type = null)
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        if($type) self::$Instance->SetType($type);

        return self::$Instance;
    }

    public function __construct($type = null){
        if($type) {

            $this->SetType($type);

        }
    }

    public function SetType($type)
    {
        $this->type = $type;
    }

    public function GetType()
    {
        return $this->type;
    }

    public function GetTypePluralized()
    {
        return $this->GetType().'s';
    }

    protected function GetParameterKey($parameterName)
    {
        $parameters = $this->GetParameters();

        if (isset($parameters[$parameterName])) {

            return $parameterName;

        }

        foreach ($parameters as $field => $value) {

            if (mb_strtolower($parameterName, 'utf-8') === mb_strtolower($field, 'utf-8')) {

                return $field;

            }

        }

    }

    public function SetParameter($parameterName, $value, $paramChanged = true)
    {
        $parameterOriginalName = $this->GetParameterKey($parameterName);

        if (null !== $parameterOriginalName) {

            $parameterName = $parameterOriginalName;

        }

        $this->parameters[$parameterName] = $value;

        if ($paramChanged) {

            $this->parametersChanged[$parameterName] = $value;

        }

        return $this;
    }

    public function GetParameter($parameterName)
    {
        $parameterOriginalName = $this->GetParameterKey($parameterName);

        if (null === $parameterOriginalName) {

            throw new \Exception('Field name "' . $parameterName . '" not found');

        }

        return $this->parameters[$parameterOriginalName];

    }

    public function GetParameters()
    {
        return $this->parameters;
    }

    public function GetParametersChanged()
    {
        return $this->parametersChanged;
    }

    public function IsNew()
    {
        if (isset($this->parameters['id'])) {

            return false;

        }
        return true;
    }

}
