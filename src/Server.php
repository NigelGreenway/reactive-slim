<?php
declare(strict_types=1);
namespace ReactiveSlim;

use React\ {
    EventLoop\Factory as EventLoop,
    EventLoop\LoopInterface,
    Http\Request as ReactRequest,
    Http\Response as ReactResponse,
    Socket\Server as SocketServer,
    Http\Server as HttpServer
};
use Slim\{
    App as SlimInstance,
    Http\Cookies as SlimCookies,
    Http\Response as SlimResponse
};
use Zend\Diactoros\ {
    ServerRequest,
    Stream
};

final class Server
{
    /** @var SlimInstance  $slimInstance*/
    private $slimInstance;
    /** @var string        $webRoot */
    private $webRoot;
    /** @var string        $host */
    private $host = '0.0.0.0';
    /** @var int           $port */
    private $port = 1337;
    /** @var int           $environment */
    private $environment = ServerEnvironment::PRODUCTION;
    /** @var LoopInterface $loop */
    private $loop;
    /** @var HttpServer    $server */
    private $server;
    /** @var string        $whiteListedAssetFileTypes */
    private $whiteListedAssetFileTypes = 'css|css.map|png|jpg|jpeg|gif|js|js.map';


    /**
     * @param SlimInstance $slimInstance
     * @param string       $directoryPath | null
     */
    public function __construct(
        SlimInstance $slimInstance,
        string       $directoryPath = null
    ) {
        $this->isAValidDirectory($directoryPath);
        $this->setDefaults();

        $this->webRoot = $directoryPath;
        $this->slimInstance = $slimInstance;
    }

    /**
     * @param string $host
     * @return Server
     */
    public function setHost(string $host) :self
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param int $port
     * @return Server
     */
    public function setPort(int $port) :self
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param int $environment
     * @return Server
     */
    public function setEnvironment(int $environment) :self
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @param array|string $fileTypes
     * @return Server
     */
    public function setAllowedFileTypes($fileTypes) :self
    {
        if (is_array($fileTypes) === true) {
            $fileTypeString = '';
            foreach ($fileTypes as $loopIndex => $fileType) {
                $fileTypeString .=  $loopIndex === 0 ? $fileType : '|' . $fileType;
            }
            $this->whiteListedAssetFileTypes = $fileTypeString;
            return $this;
        }

        if (is_string($fileTypes) === true) {
            $this->whiteListedAssetFileTypes = $fileTypes;
            return $this;
        }
    }

    /** @return void */
    public function run()
    {
        $this->initialiseReactPHP();

        $this->server->on('request', function (ReactRequest $request, ReactResponse $response) {

            $fileTypes = sprintf('/\.(?:%s)$/', $this->whiteListedAssetFileTypes);

            if (preg_match($fileTypes, $request->getPath())) {
                $assetFilePath = sprintf('%s%s', $this->webRoot, $request->getPath());
                if (is_file($assetFilePath) === true) {
                    $response->writeHead(200, ['Content-Type' => $request->getHeaders()['Accept'][0]]);
                    $response->end(file_get_contents($assetFilePath));
                } else {
                    $response->writeHead(404, ['Content-Type' => 'text/plain']);
                    $response->end(sprintf('%s was not found', $request->getPath()));
                }
            } else {
                $stream = new Stream('php://memory', 'w+');

                $request->on('data', function ($data) use ($stream) {
                    $stream->write($data);
                });

                $request->on('end', function () use ($request, $response, $stream) {
                    $stream->rewind();

                    $psr7Request = new ServerRequest(
                        [],
                        [],
                        $request->getPath(),
                        $request->getMethod(),
                        $stream,
                        $request->getHeaders(),
                        SlimCookies::parseHeader($request->getHeader('Cookie')),
                        $request->getQueryParams()
                    );

                    $slimResponse = $this->slimInstance->process($psr7Request, new SlimResponse());
                    $response->writeHead($slimResponse->getStatusCode(), $slimResponse->getHeaders());
                    $slimResponse->getBody()->rewind();
                    $response->end($slimResponse->getBody()->getContents());
                });
            }
        });

        if ($this->environment !== ServerEnvironment::PRODUCTION) {
            $output = sprintf(
                " >> Listening on http://%s:%d\n\nIn %s environment\n\n",
                $this->host,
                $this->port,
                ServerEnvironment::getEnvironmentName($this->environment)
            );
            $terminal = fopen('php://stdout', 'w');
            fwrite($terminal, $output);
            fclose($terminal);
        }

        $this->loop->run();
    }

    /**
     * @return array
     */
    public function getConfigVariables() :array
    {
        return [
            'host'                  => $this->host,
            'port'                  => $this->port,
            'env'                   => $this->environment,
            'allowedAssetFileTypes' => $this->whiteListedAssetFileTypes,
        ];
    }

    /**
     * Initialise ReactPHP setup
     * @return void
     */
    private function initialiseReactPHP()
    {
        $loop         = EventLoop::create();
        $socketServer = new SocketServer(
            sprintf('%s:%d', $this->host, $this->port),
            $loop
        );
        $this->loop    = $loop;
        $this->server  = new HttpServer($socketServer);
    }

    /**
     * @return void
     */
    private function setDefaults()
    {
        $options = getopt('p::h::', ['port::', 'host::']);

        $port = $options['p'] ?? $options['port'] ?? 1337;
        $this->setPort((int) $port);

        $host = $options['h'] ?? $options['host'] ?? '0.0.0.0';
        $this->setHost($host);
    }

    /**
     * @param string|null $directoryPath
     *
     * @throws DirectoryNotFound
     * @return void
     */
    public function isAValidDirectory($directoryPath)
    {
        if ($directoryPath !== null
            && is_dir($directoryPath) === false
        ) {
            throw new DirectoryNotFound($directoryPath);
        }
    }
}
