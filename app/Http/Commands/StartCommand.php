<?php

namespace App\Http\Commands;

use App\Http\Helpers\ResponseHelper;
use Mix\Console\CommandLine\Flag;
use Mix\Helper\ProcessHelper;
use Mix\Http\Message\ServerRequest;
use Mix\Http\Message\Response;
use Mix\Http\Server\Server;
use Mix\Http\Server\Middleware\MiddlewareDispatcher;
use Mix\Log\Logger;
use Mix\Route\Router;

/**
 * Class StartCommand
 * @package App\Http\Commands
 * @author liu,jian <coder.keda@gmail.com>
 */
class StartCommand
{

    /**
     * @var Server
     */
    public $server;

    /**
     * @var Logger
     */
    public $log;

    /**
     * @var Router
     */
    public $route;

    /**
     * StartCommand constructor.
     */
    public function __construct()
    {
        $this->log    = context()->get('log');
        $this->route  = context()->get('route');
        $this->server = context()->get('httpServer');
    }

    /**
     * 主函数
     */
    public function main()
    {
        // 参数重写
        $host = Flag::string(['h', 'host'], '');
        if ($host) {
            $this->server->host = $host;
        }
        $port = Flag::string(['p', 'port'], '');
        if ($port) {
            $this->server->port = $port;
        }
        $reusePort = Flag::bool(['r', 'reuse-port'], false);
        if ($reusePort) {
            $this->server->reusePort = $reusePort;
        }
        // 捕获信号
        ProcessHelper::signal([SIGINT, SIGTERM, SIGQUIT], function ($signal) {
            $this->log->info('received signal [{signal}]', ['signal' => $signal]);
            $this->log->info('server shutdown');
            $this->server->shutdown();
            ProcessHelper::signal([SIGINT, SIGTERM, SIGQUIT], null);
        });
        // 启动服务器
        $this->start();
    }

    /**
     * 启动服务器
     */
    public function start()
    {
        $server = $this->server;
        $server->handle('/', function (ServerRequest $request, Response $response) {
            $this->handle($request, $response);
        });
        $server->set([
            //...
        ]);
        $this->welcome();
        $this->log->info('server start');
        $server->start();
    }

    /**
     * 请求处理
     * @param ServerRequest $request
     * @param Response $response
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function handle(ServerRequest $request, Response $response)
    {
        // 路由匹配
        try {
            $result = $this->route->match($request->getMethod(), $request->getServerParams()['path_info'] ?: '/');
        } catch (\Throwable $e) {
            // 404 处理
            static::show404($e, $response);
            return;
        }
        // 保存路由参数
        foreach ($result->getParams() as $key => $value) {
            $request->withAttribute($key, $value);
        }
        // 执行
        try {
            // 执行中间件
            $dispatcher = new MiddlewareDispatcher($result->getMiddleware(), $request, $response);
            $response   = $dispatcher->dispatch();
            // 执行控制器
            if (!$response->getBody()) {
                $response = call_user_func($result->getCallback($request, $response), $request, $response);
            }
            /** @var Response $response */
            $response->end();
        } catch (\Throwable $e) {
            // 500 处理
            static::show500($e, $response);
            // 抛出错误，记录日志
            throw $e;
        }
    }

    /**
     * 404处理
     * @param \Throwable $e
     * @param Response $response
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     */
    public static function show404(\Throwable $e, Response $response)
    {
        $response = ResponseHelper::view($response, 'errors.not_found', [
            'message' => $e->getMessage(),
            'type'    => get_class($e),
            'code'    => $e->getCode(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);
        $response->withStatus(404)->end();
    }

    /**
     * 500处理
     * @param \Throwable $e
     * @param Response $response
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     */
    public static function show500(\Throwable $e, Response $response)
    {
        $response = ResponseHelper::view($response, 'errors.internal_server_error', [
            'message' => $e->getMessage(),
            'type'    => get_class($e),
            'code'    => $e->getCode(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);
        $response->withStatus(500)->end();
    }

    /**
     * 欢迎信息
     */
    protected function welcome()
    {
        $phpVersion    = PHP_VERSION;
        $swooleVersion = swoole_version();
        $host          = $this->server->host;
        $port          = $this->server->port;
        echo <<<EOL
                              ____
 ______ ___ _____ ___   _____  / /_ _____
  / __ `__ \/ /\ \/ /__ / __ \/ __ \/ __ \
 / / / / / / / /\ \/ _ / /_/ / / / / /_/ /
/_/ /_/ /_/_/ /_/\_\  / .___/_/ /_/ .___/
                     /_/         /_/


EOL;
        println('Server         Name:      mix-httpd');
        println('System         Name:      ' . strtolower(PHP_OS));
        println("PHP            Version:   {$phpVersion}");
        println("Swoole         Version:   {$swooleVersion}");
        println('Framework      Version:   ' . \Mix::$version);
        println("Listen         Addr:      {$host}");
        println("Listen         Port:      {$port}");
    }

}
