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

    public function test_Json_data_is_returned_in_the_body()
    {
        $data = json_encode(['name' => 'Jason']);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        $ch = curl_init('http://0.0.0.0:1351/json-post');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->assertEquals('{"name":"Jason"}', $result);
    }
}
