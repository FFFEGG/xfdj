<?php

namespace App\Tenancy\Controllers;

use App\Active;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AvtiveController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '意向列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Active);
        $grid->model()->orderBy('created_at','desc');
        $grid->column('id', __('Id'));
        $grid->column('name', __('姓名'));
        $grid->column('tel', __('电话'));
        $grid->column('qhd', __('取货点'))->display(function ($v){
           return $v?'<p style="color: red;font-weight: 600">√</p>':'X';
        });
        $grid->column('sqsh', __('社区商户'))->display(function ($v){
            return $v?'<p style="color: red;font-weight: bold">√</p>':'X';
        });
        $grid->column('sqdl', __('社区代理'))->display(function ($v){
            return $v?'<p style="color: red;font-weight: bold">√</p>':'X';
        });
        $grid->column('gys', __('供应商'))->display(function ($v){
            return $v?'<p style="color: red;font-weight: bold">√</p>':'X';
        });
        $grid->column('created_at', __('申请时间'));

        $grid->column('status','是否处理')->switch();
        $grid->column('msg','备注')->editable();

        $grid->disableCreateButton();
        $grid->disableActions();
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
        $show = new Show(Active::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('tel', __('Tel'));
        $show->field('qhd', __('Qhd'));
        $show->field('sqsh', __('Sqsh'));
        $show->field('sqdl', __('Sqdl'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('gys', __('Gys'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Active);

        $form->text('name', __('Name'));
        $form->text('tel', __('Tel'));
        $form->number('qhd', __('Qhd'));
        $form->number('sqsh', __('Sqsh'));
        $form->number('sqdl', __('Sqdl'));
        $form->switch('gys', __('Gys'));
        $form->switch('status', __('status'));
        $form->text('msg', __('msg'));

        return $form;
    }
}
