<?php

require_once 'CheckingAutoloader.php';
\spl_autoload_register(['CheckingAutoloader', 'autoload'], true, true);

define('DSU', DIRECTORY_SEPARATOR);
define('ROOT_DIR', __DIR__ . DSU);
define('UP_DIR', ROOT_DIR  . 'uploadTestFiles' . DSU);
define('FILE_DIR', ROOT_DIR . 'files' . DSU);









