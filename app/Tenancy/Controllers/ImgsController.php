<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use App\Img;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ImgsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('图片管理')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('图片管理')
            ->description('修改')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('图片管理')
            ->description('新增')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Img);

        $grid->id('Id');
        $grid->type('图片类型')->radio([0 => '登录页背景图', 1 => '轮播图', 2 => '产品详情背景',3=>'指纹锁',4=>'驾校',5=>'净化器',6=>'其他',7=>'餐厅',8=>'优惠券']);
        $grid->image('图片')->image();
        $grid->url('链接');
        $grid->url_type('跳转类型')->radio([0 => '无', 1 => '跳转网页', 2 => '跳转产品详情页']);
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

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
        $show = new Show(Img::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->image('Image');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Img);

        $form->radio('type', '图片类型')->options([0 => '登录页背景图', 1 => '轮播图', 2 => '产品详情背景',3=>'指纹锁',4=>'驾校',5=>'净化器',6=>'其他',7=>'餐厅',8=>'优惠券']);
        $form->radio('url_type', '跳转类型')->options([0 => '无', 1 => '跳转网页', 2 => '跳转产品详情页']);
        $form->text('url', '链接');
        $form->image('image', '图片')->uniqueName();

        return $form;
    }
}
