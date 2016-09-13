<?php

namespace cahuk\checking\components;

/**
 * Class LinkAvailableCheck
 *
 * @package cahuk\checking\components
 */
class FileMaxSizeCheck extends ACheck\AbstractSpecificCheck
{
    private $maxSize = 2621440; // 2.5 * 1024 * 1024 = 2621440 Kb. Max size of file

    /**
     * @return boolean
     */
    protected function checking()
    {
        $filePath = $this->getDataCheck();
        $file = new \SplFileInfo($filePath);
        if($file->isFile() && $file->getSize() <= $this->getMaxSize()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param int $maxSize Kb
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

}