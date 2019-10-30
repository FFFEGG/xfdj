<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token(), false); ?>">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo e(asset('vendor/prequel/favicon.png'), false); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/prequel/app.css'), false); ?>">

    <title><?php echo e((config('app.name')), false); ?> Prequel</title>
</head>
<body class="overflow-x-auto overflow-y-scroll bg-page">

<div id="prequel"></div>

<script>
  // Pass initial data to JavaScript
  window.Prequel       = {};
  window.Prequel.env   = <?php echo json_encode($env, 15, 512) ?>;
  window.Prequel.error = {
    error   : false,
    detailed: '',
    code    : '',
  };
  window.Prequel.data  = <?php echo json_encode($data['collection'], 15, 512) ?>;
  window.Prequel.flat  = <?php echo json_encode($data['flatTableCollection'], 15, 512) ?>;
</script>
<script src="<?php echo e(asset('/vendor/prequel/app.js'), false); ?>"></script>
</body>
</html>
<?php /**PATH /www/wwwroot/xfdj/vendor/protoqol/prequel/resources/views/main.blade.php ENDPATH**/ ?>