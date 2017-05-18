<?php
declare(strict_types=1);
namespace spec\ReactiveSlim;

use ReactiveSlim\DirectoryNotFound;
use ReactiveSlim\Server;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReactiveSlim\ServerEnvironment;
use Slim\App;


class ServerSpec extends ObjectBehavior
{
    function let(App $slimApp)
    {
        $this->beConstructedWith($slimApp);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Server::class);
    }

    function it_should_except_a_host_string_and_return_itself()
    {
        $this->setHost('0.0.0.0')->shouldBeAnInstanceOf(Server::class);
    }

    function it_should_except_a_port_number_and_return_itself()
    {
        $this
            ->setPort(1337)
            ->shouldBeAnInstanceOf(Server::class);
    }

    function it_should_return_itself_and_be_in_a_dev_env()
    {
        $this
            ->setEnvironment(ServerEnvironment::DEVELOPMENT)
            ->shouldBeAnInstanceOf(Server::class);
    }

    function it_should_contain_a_valid_asset_directory(App $slimInstance)
    {
        $this
            ->beConstructedWith($slimInstance, __DIR__);
    }

    function it_should_throw_a_DirectoryNotFound_exeption(App $slimInstance)
    {
        $this
            ->shouldThrow(DirectoryNotFound::class)
            ->during('__construct', [$slimInstance, __DIR__.'/../directory_does_not_exists']);
    }
}
