<?php

namespace App\Cafe\Controllers;

use App\CafeCate;
use App\CafeGoods;
use App\Http\Controllers\Controller;
use App\Restaurant;
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
            ->header('菜品')
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
            ->header('菜品')
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
            ->header('菜品')
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
        $grid = new Grid(new CafeGoods);

        $grid->id('Id');
        $grid->cafe_id('餐厅')->display(function ($v){
            return Restaurant::find($v)->title;
        })->label();
        $grid->cate_id('分类')->display(function ($v){
            return CafeCate::find($v)->name;
        })->label();
        $grid->title('商品标题');
        $grid->thumb('图片')->image(env('APP_URL').'/uploads/',100);
        $grid->price('价格');
        $grid->old_price('原价');
        $grid->is_sj('是否上架')->switch();
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

        $form->select('cafe_id', '餐厅')->options('/cafe/getCafelist')->load('cate_id', '/cafe/getCafecatelist');
        $form->select('cate_id', '分类');
        $form->text('title', '标题');
        $form->text('desc', '简介');
        $form->image('thumb', '图片')->uniqueName();
        $form->decimal('price', '价格');
        $form->decimal('old_price', '原价');
        $form->switch('is_sj', '是否上架')->default(true);
        $form->text('spec_name', '规格名称');
        $form->hasMany('specs', '添加规格值', function (Form\NestedForm $form) {
            $form->text('name','规格值');
        });
        return $form;
    }
}
