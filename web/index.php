<?php
define('DS', DIRECTORY_SEPARATOR);
define('DOWNLOADS_PATH', __DIR__ . DS . '..' . DS . 'downloads');

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \cahuk\checking\components\LinkAvailableCheck;
use \cahuk\checking\components\exceptions\linkIsNotAvailableException;
use \anlutro\cURL\cURL;
use \duncan3dc\DomParser\HtmlParser;
use \cahuk\imageDownloader\UrlImagesLinkParser;
use \cahuk\imageDownloader\ContentDownloader;

$app = new Silex\Application();

$app->before(function () use ($app) {
    // Registering Twig
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => realpath(__DIR__.'/views'),
    ));
});

// first page
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

// post method has ulr for parsing images
$app->post('/', function (Request $request) use ($app) {

    try {
        $url = $request->get('url');
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

        /** @var ContentDownloader  $downloader */
        $downloader = new ContentDownloader(DOWNLOADS_PATH);
        $downloader->setDownloadLinks($images);
        $downloader->setSubDir(date("dmY_h-i-s"));
        $downloader->run();

        /** @var  $uploadedImages */
        $uploadedImages = $downloader->getUploaded();

        /** @var array $imagesBadMimeTypes */
        $imagesBadMimeTypes = $downloader->getBadMimeTypes();

    } catch(linkIsNotAvailableException $e) {
        return new Response("Указанная ссылка: $url недоступна!");
    } catch(\anlutro\cURL\cURLException $e) {
        return new Response("Произошла ошибка, повторите попытку позже!");
    }

    return $app['twig']->render('result.twig', [
        'allImages' => $images,
        'url' => $url,
        'uploadedImages' => $uploadedImages,
        'imagesBadMimeTypes' => $imagesBadMimeTypes,
        'directoryPath' => realpath($downloader->getFullPath()),
    ]);
});

$app->run();