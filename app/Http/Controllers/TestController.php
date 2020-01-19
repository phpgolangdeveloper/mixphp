<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Http\Service\TestService;
use Mix\Http\Message\ServerRequest;
use Mix\Http\Message\Response;
use App\Http\Helpers\SendHelper;

class TestController
{
    public function index(ServerRequest $request, Response $response)
    {
        $content = 'Hello, World!';

        // xgo 开启协程
        xgo(function() {
            TestService::index();
        });

        return ResponseHelper::html($response, $content);
    }

    /***
     * 获取token验证
     * @param ServerRequest $request
     * @param Response $response
     * @return Response
     */
    public function auth(ServerRequest $request, Response $response)
    {
        /* 验证账号密码成功后 */
        // 创建Token
        $time = time();
        /** @var Authorization $auth */
        $auth        = context()->get('auth');
        $payload     = [
            "iss" => "http://wjgo.com", // 签发人
            'iat' => $time, // 签发时间
            'exp' => $time + 7200, // 过期时间
            'uid' => 100008,
        ];
        $accessToken = $auth->createToken($payload);
        // 响应
        $data = [
            'status' => 0,
            'data'   => [
                'access_token' => $accessToken,
                'expire_in'    => 7200,
            ],
        ];
        return ResponseHelper::json($response, $data);
    }

    /***
     * 渲染视图
     * @param ServerRequest $request
     * @param Response $response
     * @return mixed
     */
    public function webSocketJavaScriptView(ServerRequest $request, Response $response)
    {
        $data = [
            'id'      => $request->getAttribute('id') ?: 1,
            'name'    => '小明',
            'age'     => 18,
            'friends' => ['小红', '小花', '小飞'],
        ];
        return SendHelper::view($response, 'Test.test', $data);
    }
}