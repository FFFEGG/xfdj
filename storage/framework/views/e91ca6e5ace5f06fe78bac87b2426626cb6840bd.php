<div <?php echo $attributes; ?> style='padding: 5px;border: 1px solid #f4f4f4;background-color:white;width:<?php echo e($width, false); ?>px;'>
    <ol class="carousel-indicators">

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li data-target="#<?php echo $id; ?>" data-slide-to="<?php echo e($key, false); ?>" class="<?php echo e($key == 0 ? 'active' : '', false); ?>"></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </ol>
    <div class="carousel-inner">

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item <?php echo e($key == 0 ? 'active' : '', false); ?>">
            <img src="<?php echo e(url($item['image']), false); ?>" alt="<?php echo e($item['caption'], false); ?>" style='max-width:<?php echo e($width, false); ?>px;max-height:<?php echo e($height, false); ?>px;display: block;margin-left: auto;margin-right: auto;'>
            <div class="carousel-caption">
                <?php echo e($item['caption'], false); ?>

            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
    <a class="left carousel-control" href="#<?php echo $id; ?>" data-slide="prev">
        <span class="fa fa-angle-left"></span>
    </a>
    <a class="right carousel-control" href="#<?php echo $id; ?>" data-slide="next">
        <span class="fa fa-angle-right"></span>
    </a>
</div>
<?php /**PATH /www/wwwroot/xfdj/vendor/encore/laravel-admin/src/../resources/views/widgets/carousel.blade.php ENDPATH**/ ?>