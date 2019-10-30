<?php

namespace App\Admin\Controllers;

use App\Goods;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GoodsController extends Controller
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
            ->header('Index')
            ->description('description')
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
            ->header('Edit')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods);

        $grid->id('Id');
        $grid->type('Type');
        $grid->title('Title');
        $grid->gys_price('Gys price');
        $grid->image('Image');
        $grid->gys_id('Gys id');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->is_pass('Is pass');

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
        $show = new Show(Goods::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->title('Title');
        $show->gys_price('Gys price');
        $show->image('Image');
        $show->gys_id('Gys id');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->is_pass('Is pass');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods);

        $form->number('type', 'Type');
        $form->text('title', 'Title');
        $form->decimal('gys_price', 'Gys price');
        $form->textarea('image', 'Image');
        $form->number('gys_id', 'Gys id');
        $form->number('is_pass', 'Is pass');

        return $form;
    }
}
