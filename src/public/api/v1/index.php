<?php

header('Access-Control-Allow-Origin: *');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$pdo = new PDO("mysql:host=localhost;"."dbname=id1855654_photopost","id1855654_admin","4Z32GHC5P8CG");

$app = new \Slim\App(["settings"=>$config]);

$app->run();
