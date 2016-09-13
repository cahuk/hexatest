<?php

namespace cahuk\checking\components\ACheck;

/**
 * Class AbstractSpecificCheck
 *
 * @package cahuk\checking\ACheck\components
 */
abstract class AbstractSpecificCheck extends AbstractChecking
{
    /**
     * AbstractSpecificCheck constructor.
     * @param $dataCheck mixed
     * @param null $returnVal
     */
    public function __construct($dataCheck, $returnVal=null)
    {
        if($returnVal) {
            $this->returnVal = $returnVal;
        }

        $this->dataCheck = $dataCheck;
    }
}