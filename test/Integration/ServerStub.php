<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$slim = new \Slim\App();

// Some slim configuration
$slim->get('/', function( \Psr\Http\Message\RequestInterface $req, \Psr\Http\Message\ResponseInterface $res) {
    return $res
        ->withHeader('Content-Type', 'text/html')
        ->getBody()
        ->write('<h1>Welcome to ReactiveSlim</h1>');
});

$slim->post('/json-post', function(\Psr\Http\Message\RequestInterface $req, \Psr\Http\Message\ResponseInterface $res) {
   return $res
       ->withHeader('Content-Type', 'application/json')
       ->getBody()
       ->write(
           $req->getBody()->getContents()
       );
});

(new \ReactiveSlim\Server($slim))
    ->setHost('0.0.0.0')
    ->setPort(1351)
    ->run();
