<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\SpCwxxLrExporter;
use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SpcwxxLrController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品财务信息录入';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);
        $grid->model()->orderBy('sort','asc')->orderBy('created_at','desc');
        $grid->column('id', __('商品编号'));

        $grid->column('title', __('商品名称'));
        $grid->column('price', __('售价'));
        $grid->column('cbj', __('成本价'))->editable();
        $grid->column('jhsl', __('进货税率(%)'))->editable();
        $grid->column('xssl', __('销售税率(%)'))->editable();
        $grid->column('sd', __('锁定状态'))->switch();
        $grid->enableHotKeys();
        $grid->disableCreateButton();
        $grid->disableExport(false);
        $grid->disableActions();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->exporter(new SpCwxxLrExporter());
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('cate_id', __('Cate id'));
        $show->field('title', __('Title'));
        $show->field('sy_type', __('Sy type'));
        $show->field('real_sales', __('Real sales'));
        $show->field('leader_sy', __('Leader sy'));
        $show->field('price', __('Price'));
        $show->field('old_price', __('Old price'));
        $show->field('pics', __('Pics'));
        $show->field('stock', __('Stock'));
        $show->field('sales_num', __('Sales num'));
        $show->field('ps_time', __('Ps time'));
        $show->field('end_time', __('End time'));
        $show->field('content', __('Content'));
        $show->field('is_mj', __('Is mj'));
        $show->field('is_xg', __('Is xg'));
        $show->field('xg_num', __('Xg num'));
        $show->field('is_sj', __('Is sj'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('group_sy', __('Group sy'));
        $show->field('is_yg', __('Is yg'));
        $show->field('star_time', __('Star time'));
        $show->field('is_tj', __('Is tj'));
        $show->field('sort', __('Sort'));
        $show->field('gys_id', __('Gys id'));
        $show->field('status', __('Status'));
        $show->field('is_shop', __('Is shop'));
        $show->field('coupons', __('Coupons'));
        $show->field('regions', __('Regions'));
        $show->field('min_qs', __('Min qs'));
        $show->field('is_qs', __('Is qs'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);
        $form->number('cate_id', __('Cate id'));
        $form->text('cbj', __('cbj'));
        $form->text('jhsl', __('jhsl'));
        $form->text('xssl', __('xssl'));
        $form->switch('sd', __('sd'));
        $form->submitted(function (Form $form) {
            if($form->sd){
                return false;
            };
        });
        return $form;
    }
}
