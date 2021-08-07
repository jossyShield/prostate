<?php
use Bookstore\Core\Router;
use Bookstore\Core\Request;

require_once __DIR__ . '/vendor/autoload.php';

$req = new Request();

$router = new Router();
$response = $router->route($req);
echo $response;






