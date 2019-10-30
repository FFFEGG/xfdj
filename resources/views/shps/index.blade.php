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
<meta name="csrf-token" content="{{ csrf_token() }}">
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
            <a class="nav-link {{ $active==1?'active':'' }}" href="/logc/shorder">今日订单</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $active==2?'active':'' }}" href="/logc/shorderhistory">历史订单</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $active==3?'active':'' }}" href="/logc/shordernops">未配送订单<span
                        class="badge badge-light">{{ $num }}</span></a>
        </li>
    </ul>
    @foreach($list as $v)
        <div class="card" style="width: 100%;margin-bottom: 50px">

            <div class="card-body">
                <h5 class="card-title">订单状态：{{ $v->status==0?'未配送':'已配送' }}</h5>
                @foreach($v->order->msg as $vi)
                    <div class="bg-aqua" style="clear: both">
                        <p class="card-text">
                            {{ $vi->goods->title }}
                        </p>
                        <img class="card-img-top" style="width: 45%;float: left"
                             src="/uploads/{{ $vi->goods->pics[0] }}" alt="">
                        <p style="float: left;margin-left: 20%">数量X{{ $vi->num }}</p>
                    </div>

                @endforeach
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">取货人信息：{{ $v->order->shuser->name }} <a
                            href="tel:{{  $v->order->shuser->tel  }}">{{  $v->order->shuser->tel  }}</a></li>
                <li class="list-group-item">配送至：{{  $v->order->shuser->xqname  }}<a
                            href="baidumap://map/marker?location={{ $v->order->shuser->latitude }},{{ $v->order->shuser->longitude }}&title={{ $v->order->shuser->xqname  }}&content={{ $v->order->shuser->xqname }}&src
=yourCompanyName|yourAppName">查看详细地址</a></li>
                <li class="list-group-item">提货点详细地址：{{ $v->order->shuser->address }}</li>
                <li class="list-group-item">所在小区：{{ $v->order->shuser->xqname }}</li>
                </li>
            </ul>
            @if($v->status ==0 )
            <div class="card-body">
                <a id="{{ $v->id }}" style="color: #0d6aad" class="card-link">确认送达</a>
            </div>
                @endif
        </div>

    @endforeach

    {{ $list->links() }}
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
