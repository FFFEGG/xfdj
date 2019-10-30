<?php

namespace App\Tenancy\Controllers;

use App\Merchant;
use App\ShKc;
use App\ShSmOrder;
use App\Tenancy\Extensions\ShsmorderExporter;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ShSmOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商户扫码订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ShSmOrder);
        $grid->model()->where('status',1)->orderBy('created_at','desc');
        $grid->column('id', __('Id'));
        $grid->column('u_id', __('用户'))->display(function ($v){
            return User::find($v)->avatar;
        })->image(50,50);
        $grid->column('sh_goods_id', __('商品'))->display(function ($v ){
            return ShKc::find($v)->goods->title;
        })->label();
        $grid->column('xs', __('商户'))->display(function ($v ){
            return Merchant::whereUId(ShKc::find($this->sh_goods_id)->u_id)->first()->name;
        })->label();
        $grid->column('xstx', __('商户小程序头像'))->display(function ($v ){
            return User::find(ShKc::find($this->sh_goods_id)->u_id)->avatar;
        })->image(50,50);
        $grid->column('num', __('数量'));
        $grid->column('price', __('金额'));
        $grid->column('created_at', __('时间'));
        $grid->filter(function($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->between('created_at', '购买时间')->datetime();

            // 关联关系查询
            $filter->where(function ($query) {
                $query->whereHas('shkc.goods', function ($query) {
                    $query->where('title', 'like', "%{$this->input}%");
                });
            }, '商品');

            $filter->where(function ($query) {
                $query->whereHas('shkc.user.ywy', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            }, '业务员');
        });
        $grid->disableExport(false);
        $grid->exporter(new ShsmorderExporter());
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
        $show = new Show(ShSmOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('u_id', __('U id'));
        $show->field('sh_goods_id', __('Sh goods id'));
        $show->field('num', __('Num'));
        $show->field('price', __('Price'));
        $show->field('sn', __('Sn'));
        $show->field('status', __('Status'));
        $show->field('formid', __('Formid'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ShSmOrder);

        $form->number('u_id', __('U id'));
        $form->number('sh_goods_id', __('Sh goods id'));
        $form->number('num', __('Num'));
        $form->decimal('price', __('Price'));
        $form->text('sn', __('Sn'));
        $form->number('status', __('Status'));
        $form->text('formid', __('Formid'));

        return $form;
    }
}
