<?php

namespace Ezi\UbQrPh\Exceptions;

class RequestFailedException extends \Exception
{
    private $errorCode;
    private $errorBody;
    private $requestName;

    /**
     * @param $errorCode
     * @param $errorBody
     * @param $requestName
     */
    public function __construct($errorCode, $errorBody, $requestName) {
        $this->errorCode = $errorCode;
        $this->errorBody = $errorBody;
        $this->requestName = $requestName;

        parent::__construct();
    }

    /**
     * @return false|string
     */
    public function toJson(): false|string
    {
        return json_encode([
            'errorCode' => $this->errorCode,
            'errorBody' => $this->errorBody,
            'requestName' => $this->requestName,
        ]);
    }

    /**
     * @return mixed
     */
    public function getErrorBody()
    {
        return $this->errorBody;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getRequestName()
    {
        return $this->requestName;
    }
}