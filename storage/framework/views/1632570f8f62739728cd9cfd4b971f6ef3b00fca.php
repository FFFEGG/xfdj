<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
    <title>物流中心</title>
</head>
<body>
<style>
    .title {
        padding: 1.5rem;
        position: fixed;
        top: 0;
        width: 90%;
        z-index: 999999;
        background: white;
    }

    .title div {
        float: left;
        width: 49%;
        height: 30px;
        line-height: 30px;
        text-align: center;
        border-bottom: 1px #007bff solid;
        border-radius: 3px;
        margin-right: 1%;
        color: black;
    }
</style>
<meta name="csrf-token" content="<?php echo e(csrf_token(), false); ?>">
<div class="container">
    <div class="title">
        <div>
            <a style="color: black" href="/logc/index">商城订单</a>
        </div>
        <div>
            <a href="#">社区商户订单</a>
        </div>
    </div>
    <ul class="nav nav-pills" style="margin: 20px 0;margin-top: 80px">
        <li class="nav-item">
            <a class="nav-link <?php echo e($active==1?'active':'', false); ?>" href="/logc/shorder">今日订单</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e($active==2?'active':'', false); ?>" href="/logc/shorderhistory">历史订单</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e($active==3?'active':'', false); ?>" href="/logc/shordernops">未配送订单<span
                        class="badge badge-light"><?php echo e($num, false); ?></span></a>
        </li>
    </ul>
    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card" style="width: 100%;margin-bottom: 50px">

            <div class="card-body">
                <h5 class="card-title">订单状态：<?php echo e($v->status==0?'未配送':'已配送', false); ?></h5>
                <?php $__currentLoopData = $v->order->msg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-aqua" style="clear: both">
                        <p class="card-text">
                            <?php echo e($vi->goods->title, false); ?>

                        </p>
                        <img class="card-img-top" style="width: 45%;float: left"
                             src="/uploads/<?php echo e($vi->goods->pics[0], false); ?>" alt="">
                        <p style="float: left;margin-left: 20%">数量X<?php echo e($vi->num, false); ?></p>
                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">取货人信息：<?php echo e($v->order->shuser->name, false); ?> <a
                            href="tel:<?php echo e($v->order->shuser->tel, false); ?>"><?php echo e($v->order->shuser->tel, false); ?></a></li>
                <li class="list-group-item">配送至：<?php echo e($v->order->shuser->xqname, false); ?><a
                            href="baidumap://map/marker?location=<?php echo e($v->order->shuser->latitude, false); ?>,<?php echo e($v->order->shuser->longitude, false); ?>&title=<?php echo e($v->order->shuser->xqname, false); ?>&content=<?php echo e($v->order->shuser->xqname, false); ?>&src
=yourCompanyName|yourAppName">查看详细地址</a></li>
                <li class="list-group-item">提货点详细地址：<?php echo e($v->order->shuser->address, false); ?></li>
                <li class="list-group-item">所在小区：<?php echo e($v->order->shuser->xqname, false); ?></li>
                </li>
            </ul>
            <?php if($v->status ==0 ): ?>
            <div class="card-body">
                <a id="<?php echo e($v->id, false); ?>" style="color: #0d6aad" class="card-link">确认送达</a>
            </div>
                <?php endif; ?>
        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo e($list->links(), false); ?>

</div>
</body>
<script src="/js/jquery.min.js"></script>
<script>
  $(function () {
    $('.card-link').click(function () {
      var id = $(this).attr('id');
      if (confirm('确认送到？')) {
        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          url: "/logc/psordersd",
          method: 'post',
          data: {
            id: id,
          },
          success() {
            window.location.reload()
          }
        });
      }
    })
  })
</script>
</html>
<?php /**PATH /www/wwwroot/xfdj/resources/views/shps/index.blade.php ENDPATH**/ ?>