<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.js"></script>
    <title>Document</title>
</head>

<body>
    <div>
        <center>
            <h1>Index</h1>
        </center>
        <div id="chat-box"></div>
    </div>
    <text id="content" class="message-item">

    </text>
    <script>
        ws = new WebSocket("ws://127.0.0.1:1234");
        var uid = document.getElementsByTagName('input')[0];
        ws.onopen = function() {
            // var val = uid.value;
            console.log('连接成功');
            ws.send(200)
        };
        ws.onmessage = function(e) {
            console.log(e)
            var data = JSON.parse(e.data) //转json数据
            console.log(data);
            $("#chat-box").append(data.message + '<br/>');

        };
    </script>
</body>

</html>