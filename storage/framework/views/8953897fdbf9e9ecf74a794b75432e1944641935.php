<div class="box">
    <?php if(isset($title)): ?>
    <div class="box-header with-border">
        <h3 class="box-title"> <?php echo e($title, false); ?></h3>
    </div>
    <?php endif; ?>

    <?php if( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() ): ?>
    <div class="box-header with-border">
        <div class="pull-right">
            <?php echo $grid->renderColumnSelector(); ?>

            <?php echo $grid->renderExportButton(); ?>

            <?php echo $grid->renderCreateButton(); ?>

        </div>
        <?php if( $grid->showTools() ): ?>
        <div class="pull-left">
            <?php echo $grid->renderHeaderTools(); ?>

        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php echo $grid->renderFilter(); ?>


    <?php echo $grid->renderHeader(); ?>


    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <div class="tables-container">
            <div class="table-wrap table-main">
                <table class="table " id="<?php echo e($grid->tableID, false); ?>">
                    <thead>
                        <tr>
                            <?php $__currentLoopData = $grid->visibleColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th <?php echo $column->formatHtmlAttributes(); ?>><?php echo e($column->getLabel(), false); ?><?php echo $column->renderHeader(); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>

                    <tbody>

                        <?php $__currentLoopData = $grid->rows(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr <?php echo $row->getRowAttributes(); ?>>
                            <?php $__currentLoopData = $grid->visibleColumnNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td <?php echo $row->getColumnAttributes($name); ?> class="column-<?php echo $name; ?>">
                                <?php echo $row->column($name); ?>

                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                    <?php echo $grid->renderTotalRow(); ?>


                </table>
            </div>

            <?php if($grid->leftVisibleColumns()->isNotEmpty()): ?>
            <div class="table-wrap table-fixed table-fixed-left">
                <table class="table ">
                    <thead>
                    <tr>
                        <?php $__currentLoopData = $grid->leftVisibleColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th <?php echo $column->formatHtmlAttributes(); ?>><?php echo e($column->getLabel(), false); ?><?php echo $column->renderHeader(); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $__currentLoopData = $grid->rows(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr <?php echo $row->getRowAttributes(); ?>>
                            <?php $__currentLoopData = $grid->leftVisibleColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $name = $column->getName()
                                ?>
                                <td <?php echo $row->getColumnAttributes($name); ?> class="column-<?php echo $name; ?>">
                                    <?php echo $row->column($name); ?>

                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                    <?php echo $grid->renderTotalRow($grid->leftVisibleColumns()); ?>


                </table>
            </div>
            <?php endif; ?>

            <?php if($grid->rightVisibleColumns()->isNotEmpty()): ?>
            <div class="table-wrap table-fixed table-fixed-right">
                <table class="table ">
                    <thead>
                    <tr>
                        <?php $__currentLoopData = $grid->rightVisibleColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th <?php echo $column->formatHtmlAttributes(); ?>><?php echo e($column->getLabel(), false); ?><?php echo $column->renderHeader(); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    </thead>

                    <tbody>

                    <?php $__currentLoopData = $grid->rows(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr <?php echo $row->getRowAttributes(); ?>>
                            <?php $__currentLoopData = $grid->rightVisibleColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                $name = $column->getName()
                                ?>
                                <td <?php echo $row->getColumnAttributes($name); ?> class="column-<?php echo $name; ?>">
                                    <?php echo $row->column($name); ?>

                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                    <?php echo $grid->renderTotalRow($grid->rightVisibleColumns()); ?>


                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $grid->renderFooter(); ?>


    <div class="box-footer clearfix">
        <?php echo $grid->paginator(); ?>

    </div>
    <!-- /.box-body -->
</div>
<?php /**PATH /www/wwwroot/xfdj/vendor/encore/laravel-admin/src/../resources/views/grid/fixed-table.blade.php ENDPATH**/ ?>