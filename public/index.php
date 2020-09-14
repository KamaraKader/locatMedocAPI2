<?php
require "../bootstrap.php";
use Src\Controller\PharmacieController;
use Src\Controller\LocaliteController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /pharmacie
// everything else results in a 404 Not Found
if ($uri[1] ==! 'pharmacie') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the user id is, of course, optional and must be a number:
$pharmaId = null;
if (isset($uri[2])) {
    $pharmaId = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PharmacieController and process the HTTP request:
$controller = new PharmacieController($dbConnection, $requestMethod, $pharmaId);
// pass the request method and user ID to the PharmacieController and process the HTTP request:
//$controller = new LocaliteController($dbConnection, $requestMethod, $localId);
$controller->processRequest();