<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;

class BaseDFSO
{

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var string[]
     */
    protected $headers;
    /**
     * @var string
     */
    protected $body;
    /**
     * @var Repository|Application|mixed
     */
    protected $baseUrl;
    /**
     * @var Repository|Application|mixed
     */
    protected $version;

    public function __construct()
    {
        $this->baseUrl = (config('dataforseo.mod') == 'sandbox') ? config('dataforseo.sandbox_host') : config('dataforseo.live_host');
        $this->version = config('dataforseo.version');
        $this->client = new Client();
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . config('dataforseo.token')
        ];
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function postReq($endPoint)
    {
        $request = new Request('POST', $this->baseUrl.'/'.$this->version.'/'.$endPoint, $this->headers, $this->body);
        $res = $this->client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
}
