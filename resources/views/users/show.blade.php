<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>我的二维码</title>
</head>
<body>
<style type="text/css">
    .qrcode svg {
        width: 100%;
        height: auto;
        padding: 20px;
    }
    .thumbnail {
        padding: 20px;
        border-radius: 3px;
        box-shadow:  0px 0px 10px #eee;
        margin-top: 35%;
    }
</style>


<div class="col-lg-3 col-md-3 hidden-sm hidden-xs user-info">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="media">
                <div align="center">
                    <img  class="thumbnail img-responsive" src="{{ $name }}?rand={{ rand(0,9999) }}">
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
