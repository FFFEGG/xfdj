<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>幸福家家商品部后台管理</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="alternate icon" type="image/png" href="/meizi/assets/i/favicon.png">
    <link rel="stylesheet" href="/meizi/assets/css/amazeui.min.css"/>
    <style>
        .header {
            text-align: center;
        }

        .header h1 {
            font-size: 200%;
            color: #333;
            margin-top: 30px;
        }

        .header p {
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="am-g">
        <h1>幸福家家</h1>
        <p>商品部后台管理</p>
    </div>
    <hr/>
</div>
<div class="am-g">
    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        @if (count($errors) > 0)
            @foreach($errors->all() as $error)
                <div class="am-alert am-alert-danger" data-am-alert>
                    <button type="button" class="am-close">&times;</button>{{ $error }}  </div>
            @endforeach
        @endif
        @if (session('error'))
            <div class="am-alert am-alert-danger" data-am-alert>
                <button type="button" class="am-close">&times;</button>{{ session('error')  }}  </div>
        @endif
        @if (session('success'))
            <div class="am-alert am-alert-success" data-am-alert>
                <button type="button" class="am-close">&times;</button>{{ session('success')  }}  </div>
        @endif

        <form method="post" class="am-form" id="forms" action="/commodity_store">
            @csrf
            <label for="email">账号:</label>
            <input type="text" name="username" id="email" value="{{ old('username') }}">
            <br>
            <label for="password">密码:</label>
            <input type="password" name="password" id="password" value="{{ old('password') }}">
            <br>
            <br/>
            <div class="am-cf">

                <input type="button" name=""  id="TencentCaptcha"
                       data-appid="2017009238"
                       data-cbfn="callback" value="登 录" class="am-btn am-btn-primary am-btn-sm am-fl">
            </div>
        </form>
        <hr>
        <p>© 2019 Zhongtian Century Network (Shenzhen) Co, Ltd. Nanning Branch</p>
    </div>
</div>
</body>
<script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/meizi/assets/js/amazeui.js"></script>
<script>


  $(function () {
    $('#doc-form-file').on('change', function () {
      var fileNames = '';
      $.each(this.files, function () {
        fileNames += '<span class="am-badge">' + this.name + '</span> ';
      });
      $('#file-list').html(fileNames);
    });
    $('.am-alert').alert()
  });
  window.callback = function(res){
    var form = document.getElementById('forms');
    if(res.ret === 0){
      form.submit();
    }
  }
</script>
</html>
