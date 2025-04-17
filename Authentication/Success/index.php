<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/Private/SessionManager.php"; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Authentication</title>
    <style>
        body {
            background: #222;
            color: white;
            font-family: sans-serif;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            inset: 0;
        }
    </style>
</head>
<body>
    <div>
        <h2>Success</h2>
        <p>Authentication succeeded, you will be redirected to the requested application in a few seconds.</p>
    </div>
</body>
</html>
