<?php

namespace cahuk\imageDownloader;

/**
 * Class UrlImagesLinkParser
 * @package cahuk\imageDownloader
 */
class UrlImagesLinkParser
{
    /** @var \DOMDocument  */
    protected $_htmlParser;

    /** @var array  */
    protected $_ext = [];


    public function __construct(\duncan3dc\DomParser\HtmlParser $htmlParser)
    {
        $this->_htmlParser = $htmlParser;
    }

    /**
     * @return mixed
     */
    public function getImagesLinks()
    {
        $allImages = $this->_htmlParser->getElementsByTagName('img');

        $images = [];

        foreach($allImages as $img) {
            $src = $img->getAttribute('src');

            if($this->_ext && !(in_array(substr($src, -3), $this->_ext)) ) {
                continue;
            }

            $images[] = $src;
        }

        return $images;

    }

    /**
     * @param array $ext
     */
    public function setImagesExt(array $ext)
    {
        $this->_ext = $ext;
    }

}