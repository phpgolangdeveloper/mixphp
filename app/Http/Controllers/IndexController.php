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
        $wjUsersModel = new WjUsersModeel();
        $data = $wjUsersModel->getRowById(1);
        var_dump($data);exit;
        return ResponseHelper::html($response, $content);
    }

}
