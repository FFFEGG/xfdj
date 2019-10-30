<?php

namespace App\Cafe\Controllers;

use App\CafeCate;
use App\Http\Controllers\Controller;
use App\Restaurant;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CafeCateController extends Controller
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
            ->header('菜品分类')
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
            ->header('菜品分类')
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
            ->header('菜品分类')
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
        $grid = new Grid(new CafeCate);
        $grid->model()->orderBy('sort','asc');
        $grid->id('Id');
        $grid->cafe_id('餐厅')->display(function ($v){
            return Restaurant::find($v)->title;
        })->sortable()->label();
        $grid->name('菜品分类')->label();
        $grid->is_show('前台是否显示')->switch([
            0=> '否',
            1=> '是'
        ]);
        $grid->sort('排序')->editable();
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');

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
        $show = new Show(CafeCate::findOrFail($id));

        $show->id('Id');
        $show->cafe_id('Cafe id');
        $show->name('Name');
        $show->is_show('Is show');
        $show->sort('Sort');
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
        $form = new Form(new CafeCate);

        $form->select('cafe_id', '餐厅')->options('/cafe/getCafelist');
        $form->text('name', '菜品分类');
        $form->switch('is_show', '前后是否显示')->options([0=>'否',1=>'是'])->default(1);
        $form->number('sort', '排序')->default(50);

        return $form;
    }
}
