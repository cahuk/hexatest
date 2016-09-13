<?php

namespace cahuk\checking\components;

/**
 * Class RemoteContentMaxSizeCheck
 * @package cahuk\checking\components
 */
class RemoteContentMaxSizeCheck extends LinkAvailableCheck
{
    /** @var int  */
    private $maxSize = 2621440; // 2.5 * 1024 * 1024 = 2621440 Kb. Max size of file


    /**
     * @return boolean
     */
    protected function checking()
    {
        if($res = parent::checking()) {
            if($this->headers['Content-Length'] <=  $this->maxSize) {
                return true;
            }

        }
        return false;

    }

    /**
     * @param int $maxSize Kb
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }
}