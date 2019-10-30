<?php

namespace App\Admin\Controllers;

use App\GoodsCate;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GoodsCateController extends Controller
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
            ->header('产品')
            ->description('分类')
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
            ->header('分类')
            ->description('详情')
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
            ->header('产品分类')
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
            ->header('产品分类')
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
        $states = [
            'on'  => ['value' => true, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => false, 'text' => '否', 'color' => 'danger'],
        ];

        $grid = new Grid(new GoodsCate);
        $grid->model()->orderBy('sort','asc');
        $grid->id('Id');
        $grid->name('分类名称')->editable();

        $grid->is_show('前台是否显示')->switch($states);
        $grid->sort('排序')->editable();
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            $filter->like('name','分类名称');
            // 设置created_at字段的范围查询
            $filter->between('created_at', '创建时间')->datetime();
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
        $show = new Show(GoodsCate::findOrFail($id));

        $show->id('Id');
        $show->name('分类名称');
        $show->sort('排序');
        $show->is_show('是否显示');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $states = [
            'on'  => ['value' => true, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => false, 'text' => '否', 'color' => 'danger'],
        ];
        $form = new Form(new GoodsCate);

        $form->text('name', '分类名称');
        $form->text('sort', '排序')->default(100);
        $form->switch('is_show','是否显示')->states($states);
        return $form;
    }
}
