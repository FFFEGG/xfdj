<?php $__env->startSection('content'); ?>
    <div class="row-content am-cf">

        <div class="row">

            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <div class="widget am-cf">
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">产品上传</div>
                        <div class="widget-function am-fr">
                            <a href="javascript:;" class="am-icon-cog"></a>
                        </div>
                    </div>
                    <div class="widget-body am-fr">
                        <?php if(count($errors) > 0): ?>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="am-alert am-alert-danger" data-am-alert>
                                    <button type="button" class="am-close">&times;</button><?php echo e($error, false); ?>  </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(session('success')): ?>
                            <div class="am-alert am-alert-success" data-am-alert>
                                <button type="button" class="am-close">&times;</button><?php echo e(session('success'), false); ?>

                            </div>
                        <?php endif; ?>
                        <form class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                            <?php echo csrf_field(); ?>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">产品标题 <span
                                            class="tpl-form-line-small-title">Title</span></label>
                                <div class="am-u-sm-9">
                                    <input value="<?php echo e(old('title'), false); ?>" type="text" name="title" class="tpl-form-input"
                                           id="user-name" placeholder="请输入标题文字">
                                    <small>请填写标题文字10-20字左右。</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">产品分类 <span
                                            class="tpl-form-line-small-title">Type</span></label>
                                <div class="am-u-sm-9">
                                    <select name="type" data-am-selected="{searchBox: 1}" style="display: none;">
                                        <?php $__currentLoopData = $goodstype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($v->id, false); ?>"><?php echo e($v->name, false); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">采购员 <span
                                            class="tpl-form-line-small-title">Cgy</span></label>
                                <div class="am-u-sm-9">
                                    <select name="cgy" data-am-selected="{searchBox: 1}" style="display: none;">
                                        <?php $__currentLoopData = \App\Cgy::get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($v->id, false); ?>"><?php echo e($v->name, false); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>












                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">产品图片 <span
                                            class="tpl-form-line-small-title">Images</span></label>
                                <div class="am-u-sm-9">
                                    <div class="am-form-group am-form-file">
                                        <div class="tpl-form-file-img">
                                            <img width="400" id="cropedBigImg" src="" alt="">
                                        </div>
                                        <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                            <i class="am-icon-cloud-upload"></i> 添加产品图片
                                        </button>
                                        <input name="file[]" id="doc-form-file" type="file" multiple="">
                                        <small>一次性可框选多张图片</small>
                                    </div>

                                </div>
                            </div>

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">提交
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
      $(function () {
        $('#doc-form-file').on('change', function () {
          var filePath = $(this).val(),         //获取到input的value，里面是文件的路径
            fileFormat = filePath.substring(filePath.lastIndexOf(".")).toLowerCase();
          // 检查是否是图片
          if (!fileFormat.match(/.png|.jpg|.jpeg/)) {
            error_prompt_alert('上传错误,文件格式必须为：png/jpg/jpeg');
            return;
          }
          for (i in this.files){
            $('.tpl-form-file-img').append('<img width="100" id="cropedBigImg" src="'+window.URL.createObjectURL(this.files[i])+'" alt="">')
          }
        })
      })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('supplier.common', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/xfdj/resources/views/supplier/goodsuploads.blade.php ENDPATH**/ ?>