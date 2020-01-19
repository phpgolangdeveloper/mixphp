<html>
<head>
    <title>WebSocket</title>
</head>
<body>

<?php
    echo "<pre>";
print_r($this);
print_r($this->id);
print_r($this->name);
print_r($this->age);
print_r($this->friends);
?>

<script>
    var webSocket = function () {
        ws = new WebSocket("ws://192.168.1.101:9502/websocket");
        ws.onopen = function() {
            console.log("连接成功");
        };
        ws.onmessage = function(e) {
            console.log("收到服务端的消息：" + e.data);
        };
        ws.onclose = function() {
            console.log("连接关闭");
        };
    };
    webSocket();
</script>
</body>
</html>