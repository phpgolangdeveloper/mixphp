<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use Mix\Http\Message\ServerRequest;
use Mix\Http\Message\Response;

class TestController
{
    public function index(ServerRequest $request, Response $response)
    {
        $content = 'Hello, World!';
        $db = context()->get(\Mix\Database\Connection::class);
        $data = $db->prepare("SELECT * FROM `wj_users` where id = 1")->queryAll();
        var_dump($data);
        return ResponseHelper::html($response, $content);
    }
}