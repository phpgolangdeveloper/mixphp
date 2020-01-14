<?php

namespace App\Http\Controllers;

use App\Common\Models\WjUsersModeel;
use App\Http\Helpers\ResponseHelper;
use Mix\Http\Message\ServerRequest;
use Mix\Http\Message\Response;
/**
 * Class IndexController
 * @package App\Http\Controllers
 * @author liu,jian <coder.keda@gmail.com>
 */
class IndexController
{

    /**
     * FileController constructor.
     * @param ServerRequest $request
     * @param Response $response
     */
    public function __construct(ServerRequest $request, Response $response)
    {
    }

    /**
     * Index
     * @param ServerRequest $request
     * @param Response $response
     * @return Response
     */
    public function index(ServerRequest $request, Response $response)
    {
        $content = 'Hello, World!';
        $db = context()->get(\Mix\Database\Connection::class);
        $data = $db->prepare("SELECT * FROM `wj_users` where id = 1")->queryAll();
        var_dump($data);
        return ResponseHelper::html($response, $content);
    }

}
