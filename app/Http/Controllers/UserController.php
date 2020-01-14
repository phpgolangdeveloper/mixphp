<?php

namespace App\Http\Controllers;

use App\Http\Forms\UserForm;
use App\Http\Helpers\ResponseHelper;
use App\Http\Models\UserModel;
use Mix\Http\Message\Response;
use Mix\Http\Message\ServerRequest;

/**
 * Class UserController
 * @package App\Http\Controllers
 * @author liu,jian <coder.keda@gmail.com>
 */
class UserController
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
     * Create
     * @param ServerRequest $request
     * @param Response $response
     * @return Response
     */
    public function create(ServerRequest $request, Response $response)
    {
        // 使用表单验证器
        $form = new UserForm($request->getAttributes());
        $form->setScenario('create');
        if (!$form->validate()) {
            $content = ['code' => 1, 'message' => 'FAILED', 'data' => $form->getErrors()];
            return ResponseHelper::json($response, $content);
        }

        // 执行保存数据库
        (new UserModel())->add($form);

        // 响应
        $content = ['code' => 0, 'message' => 'OK'];
        return ResponseHelper::json($response, $content);
    }

}
