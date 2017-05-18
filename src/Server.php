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
use Slim\ {
    App as SlimInstance,
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


    /**
     * @param SlimInstance $slimInstance
     * @param string       $directoryPath | null
     */
    public function __construct(
        SlimInstance $slimInstance,
        string       $directoryPath = null
    ) {
        $this->isAValidDirectory($directoryPath);

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

    /** @return void */
    public function run()
    {
        $this->initialiseReactPHP();

        $this->server->on('request', function (ReactRequest $request, ReactResponse $response) {

            if (preg_match('/\.(?:css|png|jpg|jpeg|gif)$/', $request->getPath())) {
                $body = file_get_contents($this->webRoot . $request->getPath());
                $response->writeHead(['Content-Type' => $request->getHeaders()['Accept'][0]]);
                $response->end($body);
            } else {
                $psr7Request = new ServerRequest(
                    [],
                    [],
                    $request->getPath(),
                    $request->getMethod(),
                    (new Stream('php://input', 'w+')),
                    $request->getHeaders(),
                    $request->getHeader('cookie'),
                    $request->getQueryParams()
                );

                $slimResponse = $this->slimInstance->process($psr7Request, new SlimResponse());
                $response->writeHead($slimResponse->getStatusCode(), $slimResponse->getHeaders());
                $slimResponse->getBody()->rewind();
                $response->end($slimResponse->getBody()->getContents());
            }
        });

        if ($this->environment !== ServerEnvironment::PRODUCTION) {
            echo sprintf(
                " >> Listening on http://%s:%d\n\nIn %s environment\n\n",
                $this->host,
                $this->port,
                ServerEnvironment::getEnvironmentName($this->environment)
            );
        }

        $this->loop->run();
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
