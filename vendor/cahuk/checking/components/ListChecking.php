<?php

namespace cahuk\checking\components;

/**
 * Class ListChecking
 * used for the chain checks
 *
 * @package cahuk\checking\components
 */
class ListChecking extends ACheck\AbstractChecking
{
    /**
     * @var AbstractChecking[]
     */
    protected $checks = [];

    public function addCheck(ACheck\AbstractChecking $check)
    {
        array_push($this->checks, $check);
    }

    /**
     * @return boolean
     */
    protected function checking()
    {
        $status = true;
        if($this->checks) {
            foreach ($this->checks as $check) {
                $check->check();
                if(!$check->getStatus()) {
                    $status = false;
                }

            }
        }

        return $status;
    }
}