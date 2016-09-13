<?php

namespace cahuk\checking\components\ACheck;

/**
 * Class AbstractChecked
 *
 * For use you mast realize method checking
 * thereafter, you can invoke a method check() in the context of the program
 *
 * @package cahuk\checking\ACheck
 */
abstract class AbstractChecking
{
    protected $status = false;
    protected $returnVal = null;
    protected $dataCheck = null;
    /** @var  \Exception */
    protected $throwException;

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool|mixed
     */
    public function check()
    {
        $result = $this->status = $this->checking();

        if($result==false) {
            if($this->returnVal !== null) {
                $result = $this->getReturnVal();
            }

            if($this->throwException) {
                throw $this->throwException;
            }
        }
        return $result;
    }

    /**
     * @param null $dataCheck
     */
    public function setDataCheck($dataCheck)
    {
        $this->dataCheck = $dataCheck;
    }

    /**
     * @return mixed
     */
    public function getDataCheck()
    {
        return $this->dataCheck;
    }

    /**
     * @return null
     */
    public function getReturnVal()
    {
        return $this->returnVal;
    }

    /**
     * @return boolean
     */
    abstract protected function checking();

    /**
     * @param \Exception $throwException
     */
    public function setThrowException(\Exception $throwException)
    {
        $this->throwException = $throwException;
        return $this;
    }
}