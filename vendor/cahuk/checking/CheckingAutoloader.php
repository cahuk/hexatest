<?php

/**
 * Class CheckingAutoloader
 */

class CheckingAutoloader
{
    /**
     * @param string $file
     */
    public static function autoload($file)
    {
        $file = str_replace('\\', '/', $file);
        $file = explode('/', $file);
        $file = array_pop($file);
        $filepath = ROOT_DIR . 'components/' . $file . '.php';

        if (file_exists($filepath)) {
            require_once($filepath);
        }
        else
        {
            $path = ROOT_DIR . 'components';
            $flag = true;
            CheckingAutoloader::recursive_autoload($file, $path, $flag);
        }
    }

    /**
     * @param string $file
     * @param string $path
     * @param boolean $flag
     */
    public static function recursive_autoload($file, $path, &$flag)
    {
        if (FALSE !== ($handle = opendir($path)) && $flag)
        {
            while (FAlSE !== ($dir = readdir($handle)) && $flag)
            {
                if (strpos($dir, '.') === FALSE)
                {
                    $path2 = $path .'/' . $dir;
                    $filepath = $path2 . '/' . $file . '.php';
                    if (file_exists($filepath))
                    {
                        $flag = FALSE;
                        require_once($filepath);
                        break;
                    }
                    CheckingAutoloader::recursive_autoload($file, $path2, $flag);
                }
            }
            closedir($handle);
        }
    }

}
