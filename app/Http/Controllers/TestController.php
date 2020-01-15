<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Http\Service\TestService;
use Mix\Http\Message\ServerRequest;
use Mix\Http\Message\Response;

class TestController
{
    public function index(ServerRequest $request, Response $response)
    {
        $content = 'Hello, World!';

        xgo(function() {
            TestService::index();
        });

        return ResponseHelper::html($response, $content);
    }
}