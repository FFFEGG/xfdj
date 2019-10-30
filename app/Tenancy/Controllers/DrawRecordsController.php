<?php

namespace App\Tenancy\Controllers;

use App\ActiveGoods;
use App\DrawRecord;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DrawRecordsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '抽奖记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DrawRecord);
        $grid->model()->orderBy('created_at','desc');
        $grid->column('id', __('抽奖编号'));
        $grid->column('openid', __('用户'))->display(function ($v){
            return User::whereOpenid($v)->first()->avatar;
        })->image(50,50);
        $grid->column('nickname', __('昵称'))->display(function (){
            return User::whereOpenid($this->openid)->first()->nickname;
        })->label();
        $grid->column('activegoods_id', __('奖品'))->display(function ($v){
            return ActiveGoods::find($v)->name;
        });
        $grid->column('created_at', __('中奖时间'));
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
        $show = new Show(DrawRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('openid', __('Openid'));
        $show->field('order_id', __('Order id'));
        $show->field('activegoods_id', __('Activegoods id'));
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
        $form = new Form(new DrawRecord);

        $form->number('openid', __('Openid'));
        $form->number('order_id', __('Order id'));
        $form->number('activegoods_id', __('Activegoods id'));

        return $form;
    }
}
