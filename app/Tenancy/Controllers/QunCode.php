<?php

namespace App\Tenancy\Controllers;

use App\QrCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Image;


class QunCode extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '群二维码';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new QrCode);

        $grid->column('id', __('Id'));
        $grid->column('code', __('二维码'))->image();
        $grid->column('bg', __('背景图'))->image();
        $grid->column('hc', __('合成'))->display(function (){
            return env('APP_URL').'/static/img/background.png?id='.rand(10000,99999);
        })->image();
        $grid->column('created_at', __('创建时间'));

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
        $show = new Show(QrCode::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('code', __('Code'));
        $show->field('bg', __('Bg'));
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
        $form = new Form(new QrCode);

        $form->image('code', __('二维码'))->uniqueName();
        $form->image('bg', __('背景图s'))->uniqueName();
        //保存后回调
        $form->saved(function (Form $form) {

            $code = Image::make(env('APP_URL').'/uploads/'.$form->model()->code);
            $code->resize(160, 160);
            $img = Image::make(env('APP_URL').'/uploads/'.$form->model()->bg);
            $img->resize(750, 1334);
            $img->insert($code, 'top-right', 290, 672)->save('static/img/background.png');
        });
        return $form;
    }
}
