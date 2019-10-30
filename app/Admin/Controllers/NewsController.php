<?php

namespace App\Admin\Controllers;

use App\News;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class NewsController extends Controller
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
            ->header('文章')
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
            ->header('文章')
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
            ->header('文章')
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
        $grid = new Grid(new News);
        $grid->model()->orderBy('created_at', 'desc');
        $grid->id('Id');
        $grid->column('url','链接')->display(function ($v){
            return '<a href="/news/'.$this->id.'">'.$this->title.'</a>';
        });
//        $grid->desc('描述');
//        $grid->keywords('关键字');
        $grid->type('类型')->radio([
            0=>'普通文章',
            1=>'购物流程',
            2=>'常见问题',
            3=>'关于幸福到家',
        ]);
        $grid->created_at('创建时间');
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
        $show = new Show(News::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->desc('Desc');
        $show->keywords('Keywords');
        $show->content('Content');
        $show->type('Type');
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
        $form = new Form(new News);
        $form->radio('type', '类型')->options([
            0=>'普通文章',
            1=>'购物流程',
            2=>'常见问题',
            3=>'关于幸福到家',
        ]);
        $form->text('title', '文章标题');
        $form->text('desc', '文章描述');
        $form->text('keywords', '关键字');
        $form->editor('content', '详情');
        return $form;
    }
}
