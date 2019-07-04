<?php

namespace unaspbr\Docs;

use GuzzleHttp\Client;

class Request {
    private static $initialized = false;
    private static $client;
    
    public $json = null;

    public function __construct($response)
    {
        $this->response = $response;
        $this->status_code = $response->getStatusCode();
        $this->body = (string) $response->getBody();

        if ($response->getHeader('Content-Type')[0] === 'application/json') {
            $data = json_decode((string) $response->getBody(), true, 512/* , JSON_THROW_ON_ERROR */);
            $this->json = $data['data'] ?? $data;
        }
    }

    /**
     * Inicializa a classe estática
     *
     * @return void
     */
    private static function initialize()
    {
        self::$client =  new Client([
            'base_uri' => 'https://docs.unasp.br/api/',
            'headers' => [
                'API-Token' => config('unasp_docs.token'),
            ],
            'http_errors' => false,
        ]);

        self::$initialized = true;
    }
    
    /**
     * Define o método mágico para lidar com os verbos http.
     */
    public static function __callStatic($name, $arguments)
    {
        if (!self::$initialized) {
            self::initialize();
        }
        
        if (in_array($name, ['get', 'delete', 'head', 'options', 'patch', 'post', 'put'])) {
            if (count($arguments) > 1) {
                if (in_array($name, ['get', 'head'])) {
                    $options = is_array($arguments[1]) ? http_build_query($arguments[1]) : $arguments[1];
                    $response = self::$client->$name("{$arguments[0]}?{$options}");
                } else {
                    $response = self::$client->$name($arguments[0], ['json' => $arguments[1]]);
                }
            } else {
                $response = self::$client->$name($arguments[0]);
            }
            
            return new Self($response);
        } else {
            self::$name($arguments);
        }
    }
}
