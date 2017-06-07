<?php

require_once __DIR__ . '/../vendor/autoload.php';

$slim = new \Slim\App();

// Some slim configuration
$slim->get('/', function( \Psr\Http\Message\ServerRequestInterface $req, \Psr\Http\Message\ResponseInterface $res) {

    $html = <<<HTML
<html>
    <head>
        <link rel="stylesheet" href="/asset/style.css">
    </head>
    <body>
        <h1>Welcome to <span class="reactive">Reactive</span><span class="slim">Slim</span></h1>
    </body>
</html>
HTML;

    return $res
        ->withHeader('Content-Type', 'text/html')
        ->getBody()
        ->write($html);
});

(new \ReactiveSlim\Server($slim, __DIR__.'/public'))
    ->run();
