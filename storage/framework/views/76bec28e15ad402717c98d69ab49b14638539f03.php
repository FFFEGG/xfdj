<div id="app" class=" ">

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">

                    <form method="post" class="form-horizontal">
                        <?php echo csrf_field(); ?>
                        <div class="box-body">

                            <div class="fields-group">


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">订单状态</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control"
                                               style="border: none;font-weight: bold"><?php echo e($order->status==1?'待配送':'已配送', false); ?> </p>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="id" value="<?php echo e($order->id, false); ?>">
                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">订单信息</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <div class="row">
                                                    <h5 style="float: left;margin-right: 50px;width: 200px;margin-left: 25px"> <?php echo e($v->goods->title, false); ?></h5>
                                                    <img style="float: left;margin-right: 50px" width="100"
                                                         src="/uploads/<?php echo e($v->goods->pics[0], false); ?>" alt="">
                                                    <h5 style="float: left;margin-right: 50px">数量X <?php echo e($v->num, false); ?></h5>
                                                </div>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">取货人信息</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                姓名：<?php echo e($order->name, false); ?> 电话： <?php echo e($order->tel, false); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">提货点信息</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                提货点名称：<?php echo e($order->group->title, false); ?></p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                提货点详细地址：<?php echo e($order->group->address, false); ?></p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                小区：<?php echo e($order->group->xqname, false); ?></p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                负责人姓名：<?php echo e($order->group->name, false); ?></p>
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                负责人电话：<?php echo e($order->group->tel, false); ?></p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">订单付款时间</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <p class="form-control" style="border: none;font-weight: bold">
                                                <?php echo e($order->paid_at, false); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group  ">
                                    <label for="sort" class="col-sm-2  control-label">选择配送人员</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <select class="form-control parent_id col-sm-8" name="u_id" id="" >
                                                <?php $__currentLoopData = \App\LogPerson::get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($v->id, false); ?>"><?php echo e($v->name, false); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
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
                                    <button type="submit" class="btn btn-primary">确认配送</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </section>
</div>
<?php /**PATH /www/wwwroot/xfdj/resources/views/logistic/order_ps.blade.php ENDPATH**/ ?>