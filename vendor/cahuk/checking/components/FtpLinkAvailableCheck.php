<?php

namespace cahuk\checking\components;

/**
 * Class FtpLinkAvailableCheck
 *
 * @package cahuk\checking\components
 */
class FtpLinkAvailableCheck extends ACheck\AbstractSpecificCheck
{
    protected $port = 21;
    protected $timeout = 3;
    private $ftpConnect = null;

    private $callMethod = 'connectStatus'; // default try connect to ftp by host, user, passed
    private $callMethodArg = []; // method arg

    private $availableMethods = [
        'connectStatus',
        'connectTestByUrl',
        'isFile',
    ];

    /**
     *
     * $ftpLinkAvailableCheck->setCallMethod('connectTestByUrl', ['path to remote ftp host']);
     *
     * @see $availableMethods
     * @param string $callMethod
     */
    public function setCallMethod($callMethod, array $arg = [])
    {
        if(in_array($callMethod, $this->availableMethods)) {
            $this->callMethod = $callMethod;
            $this->callMethodArg  = $arg;
        } else {
            throw new \Exception("Method $callMethod is not exists in " . print_r($this->availableMethods, true));
        }
    }

    /**
     * @return boolean
     */
    protected function checking()
    {
        $connectTest = call_user_func_array([$this, $this->callMethod], $this->callMethodArg);
        $this->closeConnect();
        return $connectTest;
    }

    /**
     * @param $ftpFullUrl
     * @return bool
     */
    public function connectTestByUrl($ftpFullUrl=null)
    {
        if(!$ftpFullUrl) {
            $ftpFullUrl = $this->getDataCheck();
        }

        $parseUrl= parse_url($ftpFullUrl);
        $connectStatus = $this->connectStatus($parseUrl['host'], $parseUrl['user'], $parseUrl['pass']);
        return $connectStatus;
    }

    /**
     * @param $ftpFullUrl
     */
    public function isFile($ftpFullUrl=null)
    {
        $isFile = false;
        if(!$ftpFullUrl) {
            $ftpFullUrl = $this->getDataCheck();
        }

        if($this->connectTestByUrl($ftpFullUrl)) {
            $res = ftp_size($this->ftpConnect, parse_url($ftpFullUrl, PHP_URL_PATH));
            if($res != -1) {
                $isFile = true;
            }
        }

        return $isFile;
    }


    /**
     * @param $host
     * @param $user
     * @param $password
     * @return bool
     */
    private function connectStatus($host, $user, $password)
    {
        $this->ftpConnect = ftp_connect($host, $this->port, $this->timeout);
        $connectStatus = @ftp_login($this->ftpConnect, $user, $password);
        return (bool) $connectStatus;
    }


    private function closeConnect()
    {
        if($this->ftpConnect)
            @ftp_close($this->ftpConnect);
    }


    function __destruct()
    {
       $this->closeConnect();
    }

}