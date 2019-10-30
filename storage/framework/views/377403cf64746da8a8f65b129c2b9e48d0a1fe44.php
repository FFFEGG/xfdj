<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>幸福家家供应商后台</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="/admins/assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/admins/assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <script src="/admins/assets/js/echarts.min.js"></script>
    <link rel="stylesheet" href="/admins/assets/css/amazeui.min.css" />
    <link rel="stylesheet" href="/admins/assets/css/amazeui.datatables.min.css" />
    <link rel="stylesheet" href="/admins/assets/css/app.css">
    <script src="/admins/assets/js/jquery.min.js"></script>

</head>

<body data-type="index">
<script src="/admins/assets/js/theme.js"></script>
<div class="am-g tpl-g">
    <!-- 头部 -->
    <header>
        <!-- logo -->
        <div class="am-fl tpl-header-logo">
            <a href="javascript:;">供应商后台</a>
        </div>
        <!-- 右侧内容 -->
        <div class="tpl-header-fluid">
            <!-- 侧边切换 -->
            <div class="am-fl tpl-header-switch-button am-icon-list">
                    <span>

                </span>
            </div>

        </div>

    </header>
    <!-- 风格切换 -->
    <div class="tpl-skiner">
        <div class="tpl-skiner-toggle am-icon-cog">
        </div>
        <div class="tpl-skiner-content">
            <div class="tpl-skiner-content-title">
                选择主题
            </div>
            <div class="tpl-skiner-content-bar">
                <span class="skiner-color skiner-white" data-color="theme-white"></span>
                <span class="skiner-color skiner-black" data-color="theme-black"></span>
            </div>
        </div>
    </div>
    <!-- 侧边导航栏 -->
    <div class="left-sidebar">
        <!-- 用户信息 -->
        <div class="tpl-sidebar-user-panel">
            <div class="tpl-user-panel-slide-toggleable">

                <a href="javascript:;" class="tpl-user-panel-action-link"> <span class="am-icon-pencil"></span> 账号设置</a>
            </div>
        </div>

        <!-- 菜单 -->
        <ul class="sidebar-nav">

            <li class="sidebar-nav-link">
                <a href="/supplier/index" <?php if($active==1): ?>class="active"<?php endif; ?>>
                    <i class="am-icon-home sidebar-nav-link-logo"></i> 首页
                </a>
            </li>
            <li class="sidebar-nav-link">
                <a href="/supplier/orders" <?php if($active==2): ?>class="active"<?php endif; ?>>
                    <i class="am-icon-table sidebar-nav-link-logo"></i> 订单管理
                </a>
            </li>
            <li class="sidebar-nav-link">
                <a href="/supplier/goodslist" <?php if($active==3): ?>class="active"<?php endif; ?>>
                    <i class="am-icon-calendar sidebar-nav-link-logo"></i> 产品列表
                </a>
            </li>
            <li class="sidebar-nav-link">
                <a href="/supplier/goodsuploads" <?php if($active==4): ?>class="active"<?php endif; ?>>
                    <i class="am-icon-wpforms sidebar-nav-link-logo"></i> 产品上传

                </a>
            </li>
            <li class="sidebar-nav-link">
                <a href="404.html">
                    <i class="am-icon-tv sidebar-nav-link-logo"></i> 注销
                </a>
            </li>

        </ul>
    </div>


    <!-- 内容区域 -->
    <div class="tpl-content-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>
</div>
<script src="/admins/assets/js/amazeui.min.js"></script>
<script src="/admins/assets/js/amazeui.datatables.min.js"></script>
<script src="/admins/assets/js/dataTables.responsive.min.js"></script>
<script src="/admins/assets/js/app.js"></script>

</body>

</html>
<?php /**PATH /www/wwwroot/xfdj/resources/views/supplier/common.blade.php ENDPATH**/ ?>