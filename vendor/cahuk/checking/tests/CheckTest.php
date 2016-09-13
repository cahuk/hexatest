<?php

require_once '../bootstrap.php';

use cahuk\checking\components\LinkAvailableCheck;
use cahuk\checking\components\FileMaxSizeCheck;
use cahuk\checking\components\FileExtensionCheck;
use cahuk\checking\components\ListChecking;
use cahuk\checking\components\FtpLinkAvailableCheck;


class CheckTest extends PHPUnit_Framework_TestCase
{

  /**
   *
   */
  public function testLinkAvailableCheck()
  {
    $linkTrueCheck =  new LinkAvailableCheck('http://gotit.com.ua/wp-content/uploads/2015/09/bloki_adsense.png');
    $linkTrueCheck->check();
    $this->assertTrue($linkTrueCheck->getStatus());


    $linkFalseCheck =  new LinkAvailableCheck('https://www.google.com/fasdg%20sagsdg%20sag'); // битая ссылка
    $linkFalseCheck->check();
    $this->assertFalse($linkFalseCheck->getStatus());


    $linkStatusDefVal =  new LinkAvailableCheck('https://www.google.com/fasdg%20sagsdg%20sag', 2); // битая ссылка
    $defVal = $linkStatusDefVal->check(); // по умолчанию вернет 2
    $this->assertTrue($defVal === 2);


    $linkStatusDefVal =  new LinkAvailableCheck('http://gotit.com.ua/wp-content/uploads/2015/09/bloki_adsense.png', 2); // доступная ссылка
    $defVal2 = $linkStatusDefVal->check(); // вернет true
    $this->assertTrue($defVal2);
    $this->assertFalse($defVal2===2);

  }


  /**
   *
   */
  public function testFileMaxSizeCheck()
  {
    $sizeTrueCheck =  new FileMaxSizeCheck(FILE_DIR . 'maxSizeTest.txt');
    $sizeTrueCheck->check();
    $this->assertTrue($sizeTrueCheck->getStatus());


    $sizeFalseCheckB =  new FileMaxSizeCheck(FILE_DIR . 'maxSizeTestBig.txt');
    $sizeFalseCheckB->check();
    $this->assertFalse($sizeFalseCheckB->getStatus());


    $sizeFalseCheck =  new FileMaxSizeCheck(FILE_DIR . 'maxSizeTest.txt');
    $sizeFalseCheck->setMaxSize(10); // set 10 Kb
    $sizeFalseCheck->check();
    $this->assertFalse($sizeFalseCheck->getStatus());
  }


  /**
   *
   */
  public function testFileExtensionCheck()
  {
    $fileExtension =  new FileExtensionCheck(FILE_DIR . 'fileExtension.png');
    $fileExtension->setExtension(['pdf', 'jpeg', 'jpg', 'png']);
    $fileExtension->check();
    $this->assertTrue($fileExtension->getStatus());
  }


  /**
   *
   */
  public function testListChecking()
  {
    $checker = new ListChecking();
    $linkTrueCheck =  new LinkAvailableCheck('http://google.com');
    $sizeTrueCheck =  new FileMaxSizeCheck(FILE_DIR . 'maxSizeTest.txt');
    $fileExtension =  new FileExtensionCheck(FILE_DIR. 'fileExtension.png');
    $fileExtension->setExtension(['pdf', 'jpeg', 'jpg', 'png']);

    $checker->addCheck($linkTrueCheck);
    $checker->addCheck($sizeTrueCheck);
    $checker->addCheck($fileExtension);

    $checker->check();

    $this->assertTrue($checker->getStatus());
  }


  /**
   *
   */
  public function testFalseListChecking()
  {
    $checker = new ListChecking();
    $linkTrueCheck =  new LinkAvailableCheck('http://google.com');
    $sizeTrueCheck =  new FileMaxSizeCheck(FILE_DIR . 'maxSizeTest.txt', 2);
    $sizeTrueCheck->setMaxSize(10);

    $fileExtension =  new FileExtensionCheck(FILE_DIR . 'fileExtension.png');
    $fileExtension->setExtension(['pdf', 'jpeg', 'jpg', 'png']);

    $checker->addCheck($linkTrueCheck);
    $checker->addCheck($sizeTrueCheck);
    $checker->addCheck($fileExtension);

    $checker->check();

    $this->assertFalse($checker->getStatus());
    // return def val
    $this->assertTrue($sizeTrueCheck->getReturnVal() == 2);
  }


  // UNCOMMENTED IF YOU WANT TO CHECK THE FILES ON FTP
  // AND REPLACE ftp_user, password, ftp_host, path_to_file
  /**
   * @throws Exception
   */
  /*public function testLinkAvailableFtp()
  {

    $linkTrueCheck =  new FtpLinkAvailableCheck('ftp://ftp_user:password@ftp_host/path_to_file');
    $linkTrueCheck->setCallMethod('isFile');
    $res = $linkTrueCheck->check();

    $this->assertTrue($res);
  }*/


  /**
   * @throws Exception
   */
  /*public function testLinkAvailableFtpDownload()
  {

    $fileUrl = 'ftp://ftp_user:password@ftp_host/path_to_file';
    $linkTrueCheck =  new FtpLinkAvailableCheck($fileUrl);
    $linkTrueCheck->setCallMethod('isFile');
    $res = $linkTrueCheck->check();
    if($res) {
      $ext = array_pop(explode('.', parse_url($fileUrl, PHP_URL_PATH)));
      $fileName = 'testLinkAvailableFtpDownload';
      $fullFileName = UP_DIR . '/testLinkAvailableFtpDownload/'. $fileName. '_' . time() . '.' . $ext;

      $fullFilePath = $fullFileName;
      file_put_contents($fullFilePath, file_get_contents($fileUrl));
    }
  }*/

}
