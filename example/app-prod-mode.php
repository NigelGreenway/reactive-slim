<?php

require_once __DIR__ . '/../vendor/autoload.php';

$slim = new \Slim\App();

// Some slim configuration
$slim->get('/', function( \Psr\Http\Message\RequestInterface $req, \Psr\Http\Message\ResponseInterface $res) {
    return $res
        ->withHeader('Content-Type', 'text/html')
        ->getBody()
        ->write('<h1>Welcome to ReactiveSlim</h1>');
});

(new \ReactiveSlim\Server($slim))
    ->run();
