<?php

namespace App\Tenancy\Controllers;

use App\ActiveGoods;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ActiveGoodsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '奖品设置';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ActiveGoods);
        $grid->model()->where('type',1);
        $grid->column('id', __('Id'));
        $grid->column('img', __('图片'))->image();
        $grid->column('name', __('奖品名称'));
        $grid->column('probability', __('中奖几率'))->editable();
        $grid->actions(function ($actions) {

            $actions->disableDelete();

            $actions->disableView();

        });
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
        $show = new Show(ActiveGoods::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('img', __('Img'));
        $show->field('probability', __('Probability'));
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
        $form = new Form(new ActiveGoods);

        $form->image('img', __('奖品图片'));
        $form->text('name', __('奖品名称'));
        $form->hidden('type', __('type'))->default(1);
        $form->decimal('probability', __('中奖几率'));

        return $form;
    }
}
