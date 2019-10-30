<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>团购</title>
</head>
<body>

<div class="nav-tabs-custom" id="app">

    <ul class="nav nav-tabs pull-right">

        <li class="" v-on:click="tabs(2)"><a href="#tab_2" data-toggle="tab" aria-expanded="false">明日团购商品</a></li>
        <li class="active" v-on:click="tabs(1)"><a href="#tab_1" data-toggle="tab" aria-expanded="true">今日团购商品</a></li>
    </ul>
    <div class="tab-content">
        <button type="button" class="btn btn-success" v-on:click="show=true">新增</button>
        <br>
        <br>
        <div class="tab-pane active" id="tab_1">
            @foreach($list as $v)
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" src="/uploads/{{ $v->pics[0] }}" alt="Card image cap" style="width: 100%">
                    <div class="card-body">
                        <h5 class="card-title">{{ $v->title }}</h5>
                        <p class="card-text">￥{{ $v->price }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="tab_2">
            明日团购商品
        </div>
        <div style="width: 95%;min-height: 700px;background: #fff;position: fixed;top: 12%;border-radius: 10px" v-show="show">
            <span v-on:click="show=false" style="float: right;background: red;color: white;padding: 2px 13px;font-size: 20px;cursor:pointer">X</span>
            <div style="clear: both"></div>

        </div>
    </div>



</div>
</body>
<script src="/js/vue.js"></script>
<script>
  var app = new Vue({
    el: '#app',
    data: {
      tab: 1,
      message: 'Hello Vue!',
      show: false
    },
    methods: {
      tabs(index) {
        this.tab = index
      }
    }
  })
</script>
</html>
