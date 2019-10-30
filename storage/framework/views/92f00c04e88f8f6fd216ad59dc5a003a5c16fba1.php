<!doctype html>
<html lang="<?php echo e(app()->getLocale(), false); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if(config('under-construction.lock_robots')): ?>
    <meta name="robots" content="noindex,nofollow">
    <?php endif; ?>

    <title><?php echo e($title, false); ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <style>
        html, body {
            font-family: 'Raleway', sans-serif;
            overflow-y: hidden;
        }
    </style>
</head>
<body>

<div id="app">
    <under-construction
            :title="<?php echo e(@json_encode($title), false); ?>"
            :back-button="<?php echo e(@json_encode($backButton), false); ?>"
            :show-button="<?php echo e(@json_encode($showButton), false); ?>"
            :hide-button="<?php echo e(@json_encode($hideButton), false); ?>"
            :show-loader="<?php echo e(@json_encode($showLoader), false); ?>"
            :total-digits="<?php echo e(@json_encode($totalDigits), false); ?>"
            :redirect-url="<?php echo e(@json_encode($redirectUrl), false); ?>">
    </under-construction>
</div>

<script src="/under/js"></script>
</body>
</html>
<?php /**PATH /www/wwwroot/xfdj/vendor/larsjanssen6/underconstruction/src/../resources/views/index.blade.php ENDPATH**/ ?>