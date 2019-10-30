<?php

namespace App\Admin\Controllers;

use App\CafeGoods;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CafeGoodsController extends Controller
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
        $grid = new Grid(new CafeGoods);

        $grid->id('Id');
        $grid->cafe_id('Cafe id');
        $grid->cate_id('Cate id');
        $grid->title('Title');
        $grid->desc('Desc');
        $grid->thumb('Thumb');
        $grid->price('Price');
        $grid->old_price('Old price');
        $grid->is_sj('Is sj');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(CafeGoods::findOrFail($id));

        $show->id('Id');
        $show->cafe_id('Cafe id');
        $show->cate_id('Cate id');
        $show->title('Title');
        $show->desc('Desc');
        $show->thumb('Thumb');
        $show->price('Price');
        $show->old_price('Old price');
        $show->is_sj('Is sj');
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
        $form = new Form(new CafeGoods);

        $form->number('cafe_id', 'Cafe id');
        $form->number('cate_id', 'Cate id');
        $form->text('title', 'Title');
        $form->text('desc', 'Desc');
        $form->text('thumb', 'Thumb');
        $form->decimal('price', 'Price');
        $form->decimal('old_price', 'Old price');
        $form->decimal('is_sj', 'Is sj')->default(1.00);

        return $form;
    }
}
