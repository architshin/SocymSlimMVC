<?php
use Slim\Factory\AppFactory;
require_once($_SERVER["DOCUMENT_ROOT"]."/../vendor/autoload.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/../containerSetups.php");
$app = AppFactory::create();
require_once($_SERVER["DOCUMENT_ROOT"]."/../routes.php");
$app->run();
