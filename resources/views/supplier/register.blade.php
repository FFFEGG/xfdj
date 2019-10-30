<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>幸福家家供应商注册</title>
    <link rel="stylesheet" href="/meizi/assets/css/amazeui.min.css">
    <link rel="stylesheet" href="/meizi/assets/css/app.css">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
</head>
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
<body>
<div class="header">
    <div class="am-g">
        <h1>幸福家家供应商注册</h1>
        <p></p>
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
        @if (session('success'))
            <div class="am-alert am-alert-success" data-am-alert>
                <button type="button" class="am-close">&times;</button>{{ session('success')  }}  </div>
        @endif
        <form class="am-form" id="form" action="/register_gys" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <fieldset class="upload_form">
                <div class="am-form-group">
                    <label for="doc-ipt-email-1">登录账号</label>
                    <input name="username" value="{{ old('username') }}" type="text" class="" id="doc-ipt-email-1"
                           placeholder="登录账号">
                </div>
                <div class="am-form-group">
                    <label for="doc-ipt-email-2">登录密码</label>
                    <input name="password" value="{{ old('password') }}" type="password" class="" id="doc-ipt-email-2"
                           placeholder="设置个密码吧">
                </div>
                <div class="am-form-group">
                    <label for="doc-ipt-email-2">确认密码</label>
                    <input name="password_confirmation" value="{{ old('password_confirmation') }}" type="password"
                           class="" id="doc-ipt-email-2" placeholder="输确认密码">
                </div>
                <div class="am-form-group">
                    <label for="doc-ipt-email-1">公司名称</label>
                    <input name="name" value="{{ old('name') }}" type="text" class="" id="doc-ipt-email-3"
                           placeholder="输入公司名称">
                </div>
                <div class="am-form-group">
                    <label for="doc-ipt-email-3">联系人电话</label>
                    <input name="tel" value="{{ old('tel') }}" type="text" class="" maxlength="11" minlength="11"
                           id="doc-ipt-email-4" placeholder="联系人电话">
                </div>
                <div class="am-form-group">
                    <label for="doc-select-1">供应商分类</label>
                    <select id="doc-select-1" name="type" value="{{ old('type') }}">
                        @foreach($list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                    <span class="am-form-caret"></span>
                </div>


                <div class="am-form-group" style="clear: both;float: left;width: 100%">
                    <label for="doc-select-1">行业分类</label>

                </div>
                <div class="am-form-group" style="clear: both;float: left;width: 100%">
                    <select name="hy_type" id="province" style="float: left;width: 50%;clear: both">
                        <option value="">请选择分类</option>
                    </select>
                    <select name="hy_type_value" id="city" style="float: left;width: 50%;">
                        <option value="">请选择分类</option>
                    </select>
                    <span class="am-form-caret"></span>
                </div>

                <img id="cropedBigImg" style="" width="100" src="" alt="">
                <div class="am-form-group am-form-file">
                    <button type="button" class="am-btn am-btn-danger am-btn-sm">
                        <i class="am-icon-cloud-upload"></i> 上传营业执照
                    </button>
                    <input name="file[]" value="{{ old('file') }}" id="doc-form-file" type="file" multiple>
                </div>


                <img id="cropedBigImgs" style="float: left;margin-bottom: 20px" width="100" src="" alt="">

                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">
                <input type="file" name="hyzz[]" style="display:none" class="hidden_uploads">


                <div class="am-form-group am-form-file" style="clear: both;float: left;margin-right: 20px">
                    <button type="button" class="am-btn am-btn-danger am-btn-sm">
                        <i class="am-icon-cloud-upload"></i> 上传行业资质证件
                    </button>
                    <input name="hyzz[]" value="{{ old('hyzz') }}" id="doc-form-files" type="file">
                </div>

                <input type="hidden" value="0" id="num">
                <a target="_blank" href="/industrys" style="float: left;clear: both">查看更多行业资质</a>
                <div id="file-list" style="clear: both"></div>
                <p>
                    <button type="button" id="TencentCaptcha"
                            data-appid="2017009238"
                            data-cbfn="callback" class="am-btn am-btn-default">提交
                    </button>
                </p>
            </fieldset>
        </form>
    </div>
</div>
</body>
<script src="/js/jquery.min.js"></script>
<script src="/meizi/assets/js/amazeui.js"></script>
<script>
  $(function () {
    //页面加载完毕后开始执行的事件
    var city_json = '{' +
      '"媒体自我推广":["自媒体号、媒体号"],' +
      '"3C数码":[' +
      '"手机",' +
      '"电脑及电脑周边",' +
      '"相机DV",' +
      '"网络存储",' +
      '"随身影音",' +
      '"办公设备",' +
      '"电玩学习机",' +
      '"电玩学习机",' +
      '"其他数码产品",' +
      '],' +
      '"家用电器":[' +
      '"常规大家电",' +
      '"厨房电器",' +
      '"生活电器",' +
      '"影音电器",' +
      '"护理保健",' +
      '"家电配件",' +
      '],' +
      '"交通":[' +
      '"车辆交易",' +
      '"汽车用品",' +
      '"车辆服务",' +
      '"二手车",' +
      '"航空服务",' +
      '"铁路服务",' +
      '"公路运输",' +
      '"其他运输",' +
      '],' +
      '"教育培训":[' +
      '"留学移民",' +
      '"学历教育",' +
      '"特殊人群教育",' +
      '"道德教育",' +
      '"企业拓展",' +
      '"艺术兴趣培训",' +
      '"语言培训",' +
      '"职业教育",' +
      '],' +
      '"网络服务":[' +
      '"游戏",' +
      '"应用软件",' +
      '"电商平台",' +
      '"其他网服",' +
      '],' +
      '"房地产":[' +
      '"房地产公司",' +
      '"新房出售",' +
      '"二手房出售",' +
      '"房屋租赁",' +
      '"物业管理",' +
      '],' +
      '"金融":[' +
      '"股票",' +
      '"基金",' +
      '"证券",' +
      '"期货外汇",' +
      '"银行/银行产品",' +
      '"保险",' +
      '"贵金属",' +
      '"典当",' +
      '"担保",' +
      '"p2p网贷平台",' +
      '"投资咨询",' +
      '"信托",' +
      '"资产管理/交易",' +
      '"融资租赁",' +
      '"现货交易",' +
      '"金融综合平台",' +
      '"第三方支付",' +
      '],' +
      '"旅游住宿":[' +
      '"宾馆酒店",' +
      '"票务预订（除机票外）",' +
      '"机票预订",' +
      '"旅游服务",' +
      '"旅游OTA综合平台",' +
      '"旅游景点",' +
      '],' +
      '"商业零售":[' +
      '"商场",' +
      '"超市",' +
      '"其他零售",' +
      '],' +
      '"食品饮料":[' +
      '"生活食材",' +
      '"休闲零食",' +
      '"饮料及冲调",' +
      '"酒水",' +
      '],' +
      '"家庭日用品":[' +
      '"厨具餐具",' +
      '"宠物用品",' +
      '"成人用品",' +
      '"日化用品",' +
      '],' +
      '"护肤彩妆":[' +
      '"保养护肤",' +
      '"美发护肤",' +
      '"香水",' +
      '"彩妆",' +
      '],' +
      '"化妆护理":[' +
      '"化妆品",' +
      '],' +
      '"母婴儿童":[' +
      '"母婴服务",' +
      '"母婴用品",' +
      '"育儿网站",' +
      '"童装",' +
      '"儿童玩具",' +
      '"宝宝食品",' +
      '],' +
      '"家居建材":[' +
      '"家装主材",' +
      '"住宅家具",' +
      '"家纺布艺",' +
      '"五金电工",' +
      '"家居饰品",' +
      '"装修设计",' +
      '],' +
      '"奢侈品":[' +
      '"奢侈品",' +
      '],' +
      '"服装鞋帽":[' +
      '"服装",' +
      '"鞋靴皮革",' +
      '"纺织辅料",' +
      '"帽子手套",' +
      '],' +
      '"箱包饰品":[' +
      '"鞋包",' +
      '"眼镜钟表",' +
      '"饰品",' +
      '],' +
      '"营销/广告/包装":[' +
      '"广告代理",' +
      '"营销机构",' +
      '"印刷",' +
      '"广告包装",' +
      '],' +
      '"本地生活":[' +
      '"婚恋交友",' +
      '"保姆家政",' +
      '"居民服务",' +
      '"摄影",' +
      '"家电维修",' +
      '"美容美发",' +
      '"运动健身",' +
      '"休闲娱乐",' +
      '"面包蛋糕",' +
      '"星座/算命",' +
      '"电影演出",' +
      '"卡券消费",' +
      '"生活超市",' +
      '"车辆养护",' +
      '"开锁",' +
      '"快递服务",' +
      '"电视购物类",' +
      '"物业服务",' +
      '"婚庆服务",' +
      '"餐饮",' +
      '],' +
      '"商务服务":[' +
      '"策划咨询",' +
      '"代理",' +
      '"会计税务",' +
      '"调查",' +
      '"拍卖",' +
      '"公关",' +
      '"配音",' +
      '"翻译",' +
      '"会展",' +
      '],' +
      '"法律服务":[' +
      '"司法鉴定",' +
      '"律师事务所",' +
      '"公证",' +
      '],' +
      '"通讯服务":[' +
      '"运营商",' +
      '"通讯服务设备",' +
      '],' +
      '"运动休闲娱乐":[' +
      '"运动户外",' +
      '"乐器",' +
      '"娱乐票务",' +
      '"宠物",' +
      '"体育器材",' +
      '"玩具模型",' +
      '"运势测算",' +
      '"休闲活动",' +
      '"彩票",' +
      '"图书影像",' +
      '"礼品收藏",' +
      '"铃声短信",' +
      '],' +
      '"特殊行业":[' +
      '"出版传媒",' +
      '"电子电工",' +
      '"化学原料制品",' +
      '"机械设备",' +
      '],' +
      '"节能环保":[' +
      '"污染处理",' +
      '"废旧回收",' +
      '"节能设备",' +
      '"环保设备",' +
      '"环境评测",' +
      '],' +
      '"安全安保":[' +
      '"防盗报警",' +
      '"保安安保",' +
      '"警用装备",' +
      '"门禁考勤",' +
      '"交通消防",' +
      '],' +
      '"工农业":[' +
      '"化工和材料",' +
      '"机械零件",' +
      '"农林牧渔",' +
      '"兽医兽药",' +
      '],' +
      '"药品":[' +
      '"药品交易",' +
      '"药品生产",' +
      '"药品信息",' +
      '],' +
      '"医疗器械":[' +
      '"医疗器械生产",' +
      '"医疗器械销售",' +
      '"假肢生产装配",' +
      '],' +
      '"医疗健康":[' +
      '"保健用品",' +
      '"保健食品",' +
      '"美容减肥保健用品",' +
      '"美容减肥保健食品",' +
      '"美容减肥保健食品",' +
      '],' +
      '"政府组织类":[' +
      '"社会组织类",' +
      '"政府机关类",' +
      '],' +
      '"公益":[' +
      '"公益",' +
      '],' +
      '"招商加盟":[' +
      '"美容减肥",' +
      '"餐饮服务",' +
      '"教育培训",' +
      '"成人用品",' +
      '"医药保健",' +
      '"汽车产品",' +
      '"招商加盟平台",' +
      '"其他招商加盟",' +
      '],' +
      '}';
    var city_obj = eval('(' + city_json + ')');
    for (var key in city_obj) {
      $("#province").append("<option value='" + key + "'>" + key + "</option>");
    }
    $("#province").change(function () {
      var now_province = $(this).val();
      $("#city").html('<option value="">请选择分类</option>');
      for (var k in city_obj[now_province]) {
        var now_city = city_obj[now_province][k];
        $("#city").append('<option value="' + now_city + '">' + now_city + '</option>');
      }
    });


    $('#doc-form-file').on('change', function () {
      var filePath = $(this).val(),         //获取到input的value，里面是文件的路径
        fileFormat = filePath.substring(filePath.lastIndexOf(".")).toLowerCase(),
        src = window.URL.createObjectURL(this.files[0]); //转成可以在本地预览的格式


      if (!fileFormat.match(/.png|.jpg|.jpeg/)) {
        alert('上传错误,文件格式必须为：png/jpg/jpeg');
        error_prompt_alert('上传错误,文件格式必须为：png/jpg/jpeg');
        return;
      }
      $('#cropedBigImg').attr('src', src);

    });
    $('.upload_form').on('change', '#doc-form-files', function () {
      var filePath = $(this).val(),         //获取到input的value，里面是文件的路径
        fileFormat = filePath.substring(filePath.lastIndexOf(".")).toLowerCase(),
        src = window.URL.createObjectURL(this.files[0]); //转成可以在本地预览的格式
      // 检查是否是图片
      if (!fileFormat.match(/.png|.jpg|.jpeg/)) {
        alert('上传错误,文件格式必须为：png/jpg/jpeg');
        error_prompt_alert('上传错误,文件格式必须为：png/jpg/jpeg');
        return;
      }
      $('#cropedBigImgs').attr('src', src);
      $('#cropedBigImgs').after('<div class="add" style="float: left;border: 1px dashed #eee; padding: 10px;margin-left: 30px;cursor:pointer">继续添加</div>');
      $(this).prev().remove()
    });
    $('.am-alert').alert();


    $('.upload_form').on('click', '.add', function () {
      var index = $('#num').val();
      $('.hidden_uploads')[index].click();
      index++;
      $('#num').val(index)
    });

    $('.hidden_uploads').on('change', function () {
      var filePath = $(this).val(),         //获取到input的value，里面是文件的路径
        fileFormat = filePath.substring(filePath.lastIndexOf(".")).toLowerCase(),
        src = window.URL.createObjectURL(this.files[0]); //转成可以在本地预览的格式
      // 检查是否是图片
      if (!fileFormat.match(/.png|.jpg|.jpeg/)) {
        alert('上传错误,文件格式必须为：png/jpg/jpeg');
        error_prompt_alert('上传错误,文件格式必须为：png/jpg/jpeg');
        return;
      }
      console.log(1111);
      $('#cropedBigImgs').after('<img class="jx" style="float: left;margin-bottom: 20px;margin-left: 20px" width="100" src="' + src + '" alt="">');
    })
  });
  window.callback = function (res) {
    var form = document.getElementById('form');
    if (res.ret === 0) {
      form.submit();
    }
  }
</script>
</html>
