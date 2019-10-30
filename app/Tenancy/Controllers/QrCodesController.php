<?php

namespace App\Tenancy\Controllers;

use App\Qrcode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Image;

class QrCodesController extends AdminController
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
        $grid = new Grid(new Qrcode);

        $grid->column('id', __('Id'));
        $grid->column('code', __('二维码'))->image();
        $grid->column('bg', __('背景图'))->image();
        $grid->column('hc', __('合成'))->display(function (){
            return env('APP_URL').'/static/img/background.png';
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
        $show = new Show(Qrcode::findOrFail($id));

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
        $form = new Form(new Qrcode);

        $form->image('code', __('二维码'))->uniqueName();
        $form->image('bg', __('背景图'))->uniqueName();
        //保存后回调
        $form->saved(function (Form $form) {

            $code = Image::make('uploads/'.$form->model()->code);
            $code->resize(160, 160);
            $img = Image::make('uploads/'.$form->model()->bg);
            $img->resize(750, 1334);
            $img->insert($code, 'top-right', 290, 672)->save('static/img/background.png');
        });
        return $form;
    }
}
