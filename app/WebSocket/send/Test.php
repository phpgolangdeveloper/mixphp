<?php

\Swoole\Coroutine\run(function () {

    $cli = new \Swoole\Coroutine\http\Client('127.0.0.1', 9502);
    $ret = $cli->upgrade("/websocket");
    if ($ret) {
        $frame = new Swoole\WebSocket\Frame();
        $frame->opcode = SWOOLE_WEBSOCKET_OPCODE_TEXT;
        $frame->data = '{"method":"join.room","params":[1012,"小明"],"id":1}';
        $cli->push($frame);
        $frame = new Swoole\WebSocket\Frame();
        $frame->opcode = SWOOLE_WEBSOCKET_OPCODE_TEXT;
        $frame->data = '{"method":"message.emit","params":["大家好"],"id":2}';
        $cli->push($frame);
        while (true) {
            $data = $cli->recv();
            if (!$data || $data instanceof \Swoole\WebSocket\CloseFrame) {
                echo "disconnect" . PHP_EOL;
                break;
            }
            var_dump($data);
        }
    }

});

?>