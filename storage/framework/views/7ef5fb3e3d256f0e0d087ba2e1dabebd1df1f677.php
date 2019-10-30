<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>幸福家家供应商后台管理</title>
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
        <p>供应商后台登录</p>
    </div>
    <hr/>
</div>
<div class="am-g">
    <div class="am-u-lg-6 am-u-md-8 am-u-sm-centered">
        <?php if(count($errors) > 0): ?>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="am-alert am-alert-danger" data-am-alert>
                    <button type="button" class="am-close">&times;</button><?php echo e($error, false); ?>  </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="am-alert am-alert-danger" data-am-alert>
                <button type="button" class="am-close">&times;</button><?php echo e(session('error'), false); ?>  </div>
        <?php endif; ?>
        <?php if(session('success')): ?>
            <div class="am-alert am-alert-success" data-am-alert>
                <button type="button" class="am-close">&times;</button><?php echo e(session('success'), false); ?>  </div>
        <?php endif; ?>

        <form method="post" class="am-form" id="forms" action="/supplier_login_store">
            <?php echo csrf_field(); ?>
            <label for="email">账号:</label>
            <input type="text" name="username" id="email" value="<?php echo e(old('username'), false); ?>">
            <br>
            <label for="password">密码:</label>
            <input type="password" name="password" id="password" value="<?php echo e(old('password'), false); ?>">
            <br>
            <br/>
            <div class="am-cf">

                <input type="button" name=""  id="TencentCaptcha"
                       data-appid="2017009238"
                       data-cbfn="callback" value="登 录" class="am-btn am-btn-primary am-btn-sm am-fl">
                <a href="/supplier_register"><input type="button" name="" value="注册供应商"
                                                    class="am-btn am-btn-default am-btn-sm am-fr"></a>
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
<?php /**PATH /www/wwwroot/xfdj/resources/views/supplier/login.blade.php ENDPATH**/ ?>