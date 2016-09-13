<?php

require_once 'bootstrap.php';
require_once './vendor/autoload.php';

use \cahuk\checking\components\LinkAvailableCheck;
use \cahuk\checking\components\exceptions\linkIsNotAvailableException;
use \anlutro\cURL\cURL;
use \duncan3dc\DomParser\HtmlParser;
use \cahuk\imageDownloader\UrlImagesLinkParser;
use \cahuk\imageDownloader\ContentDownloader;

/** @var string $url */
$url = 'http://gotit.com.ua/';

try {

    /** @var LinkAvailableCheck $checking */
    $checking = new LinkAvailableCheck($url);
    $checking->setThrowException(new linkIsNotAvailableException())->check();

    /** @var cURL $curl */
    $curl = new cURL();
    $response = $curl->get($url);

    /** @var string $htmlBody */
    $htmlBody = $response->body;

    /** @var HtmlParser $htmlDomParser */
    $htmlDomParser = new HtmlParser($htmlBody);

    /** @var UrlImagesLinkParser  $parser */
    $parser = new UrlImagesLinkParser($htmlDomParser);
    $parser->setImagesExt(['jpg', 'png', 'gif']);

    /** @var array $images */
    $images = $parser->getImagesLinks();

    echo "На странице " . $url . " доступно <strong>" . count($images) . "</strong> картинок!<br /><br />" . PHP_EOL;

    /** @var ContentDownloader  $downloader */
    $downloader = new ContentDownloader(DOWNLOADS_PATH);
    $downloader->setDownloadLinks($images);
    $downloader->setSubDir(date("dmY_h-i-s"));
    $downloader->run();

    /** @var  $uploadedImages */
    $uploadedImages = $downloader->getUploaded();

    if($uploadedImages ) {
        echo "<br />Загруженные картинки:<br />";
        foreach ($uploadedImages as $src) {
            echo $src;
            echo "<br />";
        }

        echo "<br />Картинки загружены в директорию: <strong>" . $downloader->getFullPath() . "</strong>";
    }

    /** @var array $imagesBadMimeTypes */
    $imagesBadMimeTypes = $downloader->getBadMimeTypes();
    if($imagesBadMimeTypes) {
        echo "<br />Mime types данных изображение не доступны:<br />";
        foreach($imagesBadMimeTypes as $src) {
            echo $src;
            echo "<br />";
        }
    }


} catch(linkIsNotAvailableException $e) {

    echo "Указаная ссылка: <strong>" . $url . "</strong> недоступна!";

} catch(\anlutro\cURL\cURLException $e) {

    echo "Произошла ошиюка, повторите попытку позже!";

}

