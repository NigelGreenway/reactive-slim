<?php
namespace spec\ReactiveSlim;

use ReactiveSlim\ServerEnvironment;
use PhpSpec\ObjectBehavior;

class ServerEnvironmentSpec extends ObjectBehavior
{
    function it_returns_the_correct_environment_of_Production()
    {
        $this
            ->getEnvironmentName(ServerEnvironment::PRODUCTION)
            ->shouldReturn('Production');
    }

    function it_returns_the_correct_environment_of_Staging()
    {
        $this
            ->getEnvironmentName(ServerEnvironment::STAGING)
            ->shouldReturn('Staging');
    }

    function it_returns_the_correct_environment_of_Testing()
    {
        $this
            ->getEnvironmentName(ServerEnvironment::TESTING)
            ->shouldReturn('Testing');
    }

    function it_returns_the_correct_environment_of_Development()
    {
        $this
            ->getEnvironmentName(ServerEnvironment::DEVELOPMENT)
            ->shouldReturn('Development');
    }
}
