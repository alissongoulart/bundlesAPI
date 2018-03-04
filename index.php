<?php
require_once('vendor/autoload.php');

//
//use GuzzleHttp\Client;
//use GuzzleHttp\HandlerStack;
//use GuzzleHttp\Subscriber\Oauth\Oauth1;
//
//class App
//{
//    private $endpoint = 'https://api.develop.moreapp.com/api/v1.0/'; // change to api.moreapp.com for production
//    private $config = [
//        'consumer_key' => 'partner1',
//        'consumer_secret' => '58380cf23b4c3a9bb692aee94e9b5cbf3560a187',
//        'token_secret' => '' // should stay empty
//    ];
//
//    public function __construct()
//    {
//        $stack = HandlerStack::create();
//        $middleware = new Oauth1($this->config);
//        $stack->push($middleware);
//
//        $this->client = new Client([
//            'base_uri' => $this->endpoint,
//            'handler' => $stack,
//            'auth' => 'oauth'
//        ]);
//    }
//
//    public function getCustomers()
//    {
//        $response = $this->client->get('customers');
//        $customers = json_decode($response->getBody()->getContents());
//
//        print_r($customers);
//    }
//
//    public function PostFilter()
//    {
//        $response = $this->client->post('customers/900001/folders/c900001a4/forms/2c0f6671443345c881ec50a4a7454814/registrations/filter/0', [
//            json => [
//                pageSize => 100,
//                sort => [],
//                idSelection => [],
//                query => []
//            ]
//        ]);
//
//        $registrations = json_decode($response->getBody()->getContents());
//        print_r($registrations);
//    }
//}
//
//$app = new App();
//$app->getCustomers();
// $app->PostFilter();
$controller = new \App\Controllers\Controller(new \App\Models\Bundle(), new \App\Models\Diagram());
$controller->getBroadBandCombinations();