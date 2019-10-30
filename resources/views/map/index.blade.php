<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <style type="text/css">
        body, html {
            width: 100%;
            height: 100%;
            margin: 0;
            font-family: "微软雅黑";
            font-size: 14px;
        }

        #l-map {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        #result {
            width: 100%;
            position: fixed;
            top: 0;
        }

        li {
            line-height: 28px;
        }

        .cityList {
            height: 320px;
            width: 372px;
            overflow-y: auto;
        }

        .sel_container {
            z-index: 9999;
            font-size: 12px;
            position: absolute;
            right: 0px;
            top: 0px;
            width: 140px;
            background: rgba(255, 255, 255, 0.8);
            height: 30px;
            line-height: 30px;
            padding: 5px;
        }

        .map_popup {
            position: absolute;
            z-index: 200000;
            width: 382px;
            height: 344px;
            right: 0px;
            top: 40px;
        }

        .map_popup .popup_main {
            background: #fff;
            border: 1px solid #8BA4D8;
            height: 100%;
            overflow: hidden;
            position: absolute;
            width: 100%;
            z-index: 2;
        }

        .map_popup .title {
            background: url("http://map.baidu.com/img/popup_title.gif") repeat scroll 0 0 transparent;
            color: #6688CC;
            font-weight: bold;
            height: 24px;
            line-height: 25px;
            padding-left: 7px;
        }

        .map_popup button {
            background: url("http://map.baidu.com/img/popup_close.gif") no-repeat scroll 0 0 transparent;
            cursor: pointer;
            height: 12px;
            position: absolute;
            right: 4px;
            top: 6px;
            width: 12px;
        }
    </style>
    <script type="text/javascript"
            src="https://api.map.baidu.com/api?v=2.0&ak=SCFxfgXICBAlF997YTPufWbPkUOB6ywd"></script>
    <!-- 加载百度地图样式信息窗口 -->
    <script type="text/javascript"
            src="https://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.js"></script>
    <link rel="stylesheet" href="https://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.css"/>

    <title>提货点分布图</title>
</head>
<body>
<div id="l-map"></div>
<div id="result">
    <button id="open">查看附近的提货点</button>
    <button id="close">关闭</button>
</div>

</body>
</html>
<script type="text/javascript">

  // 百度地图API功能
  var map = new BMap.Map("l-map");          // 创建地图实例
  var point = new BMap.Point(116.403694, 39.927552);  // 创建点坐标
  var geolocation = new BMap.Geolocation();
  geolocation.getCurrentPosition(function (r) {
    if (this.getStatus() == BMAP_STATUS_SUCCESS) {
      var mk = new BMap.Marker(r.point);
      map.addOverlay(mk);
      map.panTo(r.point);
    } else {
      alert('failed' + this.getStatus());
    }
  });
  map.centerAndZoom(point, 17);                 // 初始化地图，设置中心点坐标和地图级别
  map.enableScrollWheelZoom();
  map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
  var customLayer;

  function addCustomLayer(keyword) {
    if (customLayer) {
      map.removeTileLayer(customLayer);
    }
    customLayer = new BMap.CustomLayer({
      geotableId: 202291,
      q: '', //检索关键字
      tags: '', //空格分隔的多字符串
      filter: '' //过滤条件,参考http://lbsyun.baidu.com/lbs-geosearch.htm#.search.nearby
    });
    map.addTileLayer(customLayer);
    customLayer.addEventListener('hotspotclick', callback);
  }

  function callback(e)//单击热点图层
  {
    var customPoi = e.customPoi;//poi的默认字段
    var contentPoi = e.content;//poi的自定义字段
    var content = '<p style="width:280px;margin:0;line-height:20px;">地址：' + customPoi.address + '</p>';
    var searchInfoWindow = new BMapLib.SearchInfoWindow(map, content, {
      title: customPoi.title, //标题
      width: 290, //宽度
      height: 40, //高度
      panel: "panel", //检索结果面板
      enableAutoPan: true, //自动平移
      enableSendToPhone: true, //是否显示发送到手机按钮
      searchTypes: [
        BMAPLIB_TAB_SEARCH,   //周边检索
        BMAPLIB_TAB_TO_HERE,  //到这里去
        BMAPLIB_TAB_FROM_HERE //从这里出发
      ]
    });
    var point = new BMap.Point(customPoi.point.lng, customPoi.point.lat);
    searchInfoWindow.open(point);
  }

  document.getElementById("open").onclick = function () {
    addCustomLayer();
  };
  document.getElementById("open").click();
  document.getElementById("close").onclick = function () {
    if (customLayer) {
      map.removeTileLayer(customLayer);
    }
  };

</script>
