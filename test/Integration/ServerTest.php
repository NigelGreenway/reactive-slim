<?php
declare(strict_types=1);
namespace ReactiveSlim\Test\Integration;

final class ServerTest extends Bootstrap
{
    public function test_HTTP_status_code_is_200()
    {
        $response = $this->client->get($this->makeURL(''));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_response_body_is_as_expected()
    {
        $response = $this->client->get($this->makeURL(''));
        $this->assertEquals('<h1>Welcome to ReactiveSlim</h1>', $response->getBody());
    }
}
