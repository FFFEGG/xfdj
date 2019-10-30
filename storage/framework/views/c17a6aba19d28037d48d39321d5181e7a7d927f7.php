<!DOCTYPE html>
<html lang="<?php echo e(config('app.locale'), false); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token(), false); ?>">
    <title><?php echo e(Admin::title(), false); ?> <?php if($header): ?> | <?php echo e($header, false); ?><?php endif; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <?php if(!is_null($favicon = Admin::favicon())): ?>
    <link rel="shortcut icon" href="<?php echo e($favicon, false); ?>">
    <?php endif; ?>

    <?php echo Admin::css(); ?>


    <script src="<?php echo e(Admin::jQuery(), false); ?>"></script>
    <?php echo Admin::headerJs(); ?>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition <?php echo e(config('admin.skin'), false); ?> <?php echo e(join(' ', config('admin.layout')), false); ?>">

<?php if($alert = config('admin.top_alert')): ?>
    <div style="text-align: center;padding: 5px;font-size: 12px;background-color: #ffffd5;color: #ff0000;">
        <?php echo $alert; ?>

    </div>
<?php endif; ?>

<div class="wrapper">

    <?php echo $__env->make('admin::partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('admin::partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="content-wrapper" id="pjax-container">
        <?php echo Admin::style(); ?>

        <div id="app">
        <?php echo $__env->yieldContent('content'); ?>
        </div>
        <?php echo Admin::script(); ?>

        <?php echo Admin::html(); ?>

    </div>

    <?php echo $__env->make('admin::partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</div>

<button id="totop" title="Go to top" style="display: none;"><i class="fa fa-chevron-up"></i></button>

<script>
    function LA() {}
    LA.token = "<?php echo e(csrf_token(), false); ?>";
</script>

<!-- REQUIRED JS SCRIPTS <?php echo Admin::js(); ?>-->

  <script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/dist/js/app.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/jquery-pjax/jquery.pjax.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/nprogress/nprogress.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/nestable/jquery.nestable.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/toastr/build/toastr.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/laravel-admin/laravel-admin.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin-ext/chartjs/Chart.bundle.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/moment/min/moment-with-locales.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/bootstrap-fileinput/js/fileinput.min.js?v=4.5.2"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/number-input/bootstrap-number-input.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/bootstrap-fileinput/js/plugins/sortable.min.js?v=4.5.2"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/ueditor/ueditor.config.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/ueditor/ueditor.all.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/laravel-admin-ext/wang-editor/wangEditor-3.0.10/release/wangEditor.js"></script>
<script src="https://xfdj.luckhome.xyz/vendor/namet/laravel-admin-tagsinput/tagsinput.min.js?v=2"></script>

</body>
</html>
<?php /**PATH /www/wwwroot/xfdj/vendor/encore/laravel-admin/src/../resources/views/index.blade.php ENDPATH**/ ?>