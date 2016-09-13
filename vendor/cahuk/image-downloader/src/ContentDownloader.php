<?php

namespace cahuk\imageDownloader;

use cahuk\imageDownloader\exceptions\FailedCreateDirException;
use cahuk\checking\components\LinkAvailableMimeTypeCheck;
use cahuk\checking\components\RemoteContentMaxSizeCheck;
use cahuk\checking\components\ListChecking;
use cahuk\checking\components\exceptions\BadMimeTypeException;
use cahuk\checking\components\exceptions\RemoteContentIsLargerSizeException;

/**
 * Class ContentDownloader
 * @package cahuk\imageDownloader
 */
class ContentDownloader
{
    /** @var array  */
    protected $_downloadLinks = [];
    /** @var string  */
    protected $_downloadPath = '';
    /** @var string  */
    protected $_subDir = '';
    /** @var array  */
    protected $_badMimeTypes = [];
    /** @var array  */
    protected $_remoteMaxSize = [];
    /** @var array  */
    protected $_uploaded = [];

    /**
     * ContentDownloader constructor.
     * @param $path string
     */
    public function __construct($path)
    {
        $this->_downloadPath = $this->clearPathSeparator($path);
    }

    /**
     * @param array $links
     */
    public function setDownloadLinks(array $links)
    {
        $this->_downloadLinks = $links;
    }

    /**
     * start download process in loop
     */
    public function run()
    {
        if(! $this->isDirExists($this->_downloadPath))
        {
            $this->createDir($this->_downloadPath);
        }

        $this->createSubDir();

        foreach($this->_downloadLinks as $link) {
            try {
                $this->runValidate($link);

                $this->getContent($link);

                array_push($this->_uploaded, $link);
            } catch (BadMimeTypeException $e) {
                array_push($this->_badMimeTypes, $link);
            } catch(RemoteContentIsLargerSizeException $e) {
                array_push($this->_remoteMaxSize, $link);
            }
        }
    }

    protected function runValidate($link)
    {
        /** @var LinkAvailableMimeTypeCheck $checkMimeType */
        $checkMimeType = new LinkAvailableMimeTypeCheck($link);
        $checkMimeType->setAllowMimeTypes(LinkAvailableMimeTypeCheck::$mimeTypes[LinkAvailableMimeTypeCheck::MIME_TYPES_IMAGES]);
        $checkMimeType->setThrowException(new BadMimeTypeException());

        /** @var RemoteContentMaxSizeCheck $checkContentMaxSize */
        $checkContentMaxSize = new RemoteContentMaxSizeCheck($link);
        $checkContentMaxSize->setThrowException(new RemoteContentIsLargerSizeException());

        /** @var ListChecking $checkList */
        $checkList = new ListChecking();
        $checkList->addCheck($checkMimeType);
        $checkList->addCheck($checkContentMaxSize);

        $checkList->check();
    }


    /**
     * @param $link string
     */
    protected function getContent($link)
    {
        $ext  = substr($link,-3);
        $name = md5($link). '.' . $ext;
        $fullPath = $this->getFullPath() . DIRECTORY_SEPARATOR . $name;
        file_put_contents($fullPath , file_get_contents($link));
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->_downloadPath . ($this->getSubDir() ? DIRECTORY_SEPARATOR . $this->getSubDir() : '');
    }

    /**
     * @return string
     */
    public function getSubDir()
    {
        return ($this->_subDir ?  : '');
    }

    /**
     * @param string $subDir
     */
    public function setSubDir($subDir)
    {
        $this->_subDir = $this->clearPathSeparator($subDir);
    }

    /**
     * @throws FailedCreateDirException
     */
    protected function createSubDir()
    {
        if($this->_subDir) {
            $path = $this->_downloadPath . DIRECTORY_SEPARATOR . $this->_subDir;
            $this->createDir($path);
        }
    }

    /**
     * @param $dirPath
     * @return bool
     */
    protected function isDirExists($dirPath)
    {
        return ($dirPath && file_exists($dirPath) && is_dir($dirPath));
    }

    /**
     * @param $dirPath
     * @param int $mod
     * @return bool
     * @throws FailedCreateDirException
     */
    protected function createDir($dirPath, $mod = 0755)
    {
        if(! mkdir($this->clearPathSeparator($dirPath), $mod)) {
            throw new FailedCreateDirException("Can not create directory at path: " . $dirPath);
        }
        return true;
    }


    /**
     * @param $path
     * @return string
     */
    protected function clearPathSeparator($path)
    {
        $path = rtrim(preg_replace("/(\\/|\\\\)+/", DIRECTORY_SEPARATOR ,$path), DIRECTORY_SEPARATOR);
        return $path;
    }

    /**
     * @return array
     */
    public function getBadMimeTypes()
    {
        return $this->_badMimeTypes;
    }

    /**
     * @return array
     */
    public function getRemoteMaxSize()
    {
        return $this->_remoteMaxSize;
    }

    /**
     * @return array
     */
    public function getUploaded()
    {
        return $this->_uploaded;
    }

}