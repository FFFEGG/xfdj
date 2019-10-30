<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>社区代理</title>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
</head>
<style>
    html {
        width: 100%;
        float: left;
        padding-bottom: 50px;
    }

    body {
        width: 100%;
        float: left;
    }

    img {
        width: 80%;
        margin-left: 10%;
    }

    form {
        margin-top: 45px;
        padding: 20px;
        border-radius: 5px;
        width: 90%;
        margin-left: 5%;
        box-shadow: 1px 1px 1px 1px #eee;
    }

</style>
<body style="background: url('/imgs/sheqdl_background.png') no-repeat 100%/100%;padding-top: 50px">
<img src="/imgs/sqdl (2).png?id={{rand(1000,999)}}" alt="">
<img src="/imgs/sqdl (3).png?id={{rand(1000,999)}}" alt="" style="margin-top: 100px">
<form class="bg-white" action="" method="post" id="forms">
    @csrf
    @if (count($errors) > 0)
        @foreach($errors->all() as $error)
            <div class="alert alert-danger" data-am-alert>
                {{ $error }}
            </div>
        @endforeach
    @endif
    @if (session('success'))
        <div class="alert alert-success" data-am-alert>
            {{ session('success')  }}
        </div>
    @endif
    <div class="form-group row">
        <label for="inputPassword" class="col-3 col-form-label">姓名</label>
        <div class="col-9">
            <input type="text" name="activename" class="form-control">
        </div>
    </div>
    <div class="form-group row">
        <label for="inputPassword" class="col-3 col-form-label">电话</label>
        <div class="col-9">
            <input type="number" name="activetel" class="form-control">
        </div>
    </div>
    <button style="width: 100%;background: #e26c17;color: white"  type="submit" class="btn">提交申请</button>
</form>
</body>
<script src="/bootstrap/js/bootstrap.js"></script>
</html>
