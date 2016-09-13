<?php

namespace cahuk\checking\components;

/**
 * Class LinkAvailableCheck
 *
 * @package cahuk\checking\components
 */
class LinkAvailableCheck extends ACheck\AbstractSpecificCheck
{
    /** @var  array */
    protected $headers = [];

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return boolean
     */
    protected function checking()
    {
        $link = $this->getDataCheck();
        $res = $this->checkLinkStatus($link);
        return $res;
    }

    /**
     * @param $url string
     * @return bool
     */
    protected function checkLinkStatus($url)
    {
        $this->headers = get_headers($url, 1);
        $linkStatus = false;
        if(in_array("HTTP/1.1 200 OK", $this->headers) || in_array("HTTP/1.0 200 OK", $this->headers)){
            $linkStatus = true;
        }

        return $linkStatus;
    }


}