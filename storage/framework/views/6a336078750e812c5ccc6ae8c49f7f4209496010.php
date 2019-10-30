<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
    <title>物流中心</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token(), false); ?>">
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
    .title div{
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

<div class="container">
    <div class="title">
        <div>
            <a href="#">商城订单</a>
        </div>
        <div>
            <a style="color: black" href="/logc/shorder">社区商户订单</a>
        </div>
    </div>
    <ul class="nav nav-pills" style="margin: 20px 0;margin-top: 80px">
        <li class="nav-item">
            <a class="nav-link <?php echo e($active==1?'active':'', false); ?>" href="/logc/index">今日订单</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e($active==2?'active':'', false); ?>" href="/logc/history">历史订单</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo e($active==3?'active':'', false); ?>" href="/logc/nops">未配送订单<span class="badge badge-light"><?php echo e($num, false); ?></span></a>
        </li>
    </ul>
    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card" style="width: 100%;margin-bottom: 50px">

            <div class="card-body">
                <h5 class="card-title">订单状态：<?php echo e($v->status==0?'待配送':'已配送', false); ?></h5>
                <?php $__currentLoopData = $v->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-aqua" style="clear: both">
                        <p class="card-text">
                            <text><?php echo e($vi->order->items[0]->goods->title, false); ?></text>
                            <p style="font-weight: bold;text-align: right"> <img class="card-img-top" style="width: 20%;float: left" src="/uploads/<?php echo e($vi->order->items[0]->goods->pics[0], false); ?>" alt=""> 数量X<?php echo e($vi->order->items[0]->num, false); ?></p>
                        </p>

                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">取货人信息：<?php echo e($v->items[0]->order->name, false); ?> <a
                            href="tel:<?php echo e($v->items[0]->order->tel, false); ?>"><?php echo e($v->items[0]->order->tel, false); ?></a></li>
                <li class="list-group-item">提货点：<?php echo e($v->items[0]->order->group->title, false); ?><a href="baidumap://map/marker?location=<?php echo e($v->items[0]->order->group->latitude, false); ?>,<?php echo e($v->items[0]->order->group->longitude, false); ?>&title=<?php echo e($v->items[0]->order->group->xqname, false); ?>&content=<?php echo e($v->items[0]->order->group->title, false); ?>&src
=yourCompanyName|yourAppName">查看详细地址</a></li>
                <li class="list-group-item">提货点详细地址：<?php echo e($v->items[0]->order->group->address, false); ?></li>
                <li class="list-group-item">所在小区：<?php echo e($v->items[0]->order->group->xqname, false); ?></li>
                <li class="list-group-item">提货点负责人姓名：<?php echo e($v->items[0]->order->group->name, false); ?></li>
                <li class="list-group-item">提货点负责人电话：<a href="tel:<?php echo e($v->items[0]->order->group->tel, false); ?>"><?php echo e($v->items[0]->order->group->tel, false); ?></a>
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
          url: "/logc/shoppsorder",
          method: 'post',
          data: {
            id: id,
          },
          success(rew) {
            if (rew == 200) {
              window.location.reload()
            }
          }
        });
      }
    })
  })
</script>
</html>
<?php /**PATH /www/wwwroot/xfdj/resources/views/ps/index.blade.php ENDPATH**/ ?>