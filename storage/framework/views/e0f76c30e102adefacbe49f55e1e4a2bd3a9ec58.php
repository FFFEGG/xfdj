<div id="app" class=" ">

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">

                    <form method="post" class="form-horizontal" >
                        <?php echo csrf_field(); ?>
                        <div class="box-body">

                            <div class="fields-group">


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">公司名称</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                          <p class="form-control" style="border: none;font-weight: bold"> <?php echo e($gys->name, false); ?></p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">联系人电话</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold"> <?php echo e($gys->tel, false); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">供应商分类</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold"> <?php echo e(\App\GysType::find($gys->type)->name, false); ?></p>
                                        </div>
                                    </div>
                                </div>

                                 <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">行业分类</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold"> <?php echo e($gys->hy_type.'—'.$gys->hy_type_value, false); ?></p>
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">营业执照</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <a  target="_blank" href="<?php echo e($gys->file, false); ?>"> <img src="<?php echo e($gys->file, false); ?>" alt="" width="300" style="padding: 10px;border: 1px #eee solid;border-radius: 3px"></a>
                                        </div>
                                    </div>
                                </div>




                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">资质证书</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <?php $__currentLoopData = explode(',',$gys->hyzz); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a  target="_blank" href="<?php echo e($v, false); ?>"> <img src="<?php echo e($v, false); ?>" alt="" width="300" style="padding: 10px;border: 1px #eee solid;border-radius: 3px"></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sort" class="col-sm-2  control-label">申请时间</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">  <?php echo e($gys->created_at, false); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sort" class="col-sm-2  control-label">审核状态</label>
                                    <div class="col-sm-8">
                                        <div class="am-form-group">
                                            <label class="am-radio-inline">
                                                <input type="radio"  value="0" <?php if($gys->status == 0): ?> checked <?php endif; ?> name="status"> 审核中
                                            </label>
                                            <label class="am-radio-inline">
                                                <input type="radio" value="1"  <?php if($gys->status == 1): ?> checked <?php endif; ?> name="status"> 审核通过
                                            </label>
                                            <label class="am-radio-inline">
                                                <input type="radio" value="-1"  <?php if($gys->status == -1): ?> checked <?php endif; ?> name="status"> 冻结账户
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="box-footer">

                            <div class="col-md-2">
                            </div>

                            <div class="col-md-8">

                                <div class="btn-group pull-left">
                                    <button type="submit" class="btn btn-primary">提交</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </section>
</div>
<?php /**PATH /www/wwwroot/xfdj/resources/views/commodity/admingysedit.blade.php ENDPATH**/ ?>