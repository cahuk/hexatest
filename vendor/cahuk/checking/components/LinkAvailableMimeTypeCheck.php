<?php

namespace cahuk\checking\components;

/**
 * Class LinkAvailableMimeTypeCheck
 * @package cahuk\checking\components
 */
class LinkAvailableMimeTypeCheck extends LinkAvailableCheck
{
    /** images mime type name */
    const MIME_TYPES_IMAGES = 'images';

    /** @var array mime types */
    public static $mimeTypes = [
        self::MIME_TYPES_IMAGES => [
            'image/jpeg',
            'image/png',
            'image/gif'
        ],
    ];

    /** @var array  */
    protected $allowMimeTypes = [];


    protected function checking()
    {
        if($res = parent::checking()) {
            if($this->allowMimeTypes && in_array($this->headers['Content-Type'], $this->allowMimeTypes)) {
                return true;
            }

        }
        return false;
    }

    /**
     * @param array $allowMimeTypes
     */
    public function setAllowMimeTypes(array $allowMimeTypes)
    {
        $this->allowMimeTypes = $allowMimeTypes;
    }
}