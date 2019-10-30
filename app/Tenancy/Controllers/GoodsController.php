<?php

namespace App\Tenancy\Controllers;

use App\Cgy;
use App\Goods;
use App\GoodsCate;
use App\Gys;
use App\GysType;
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
            ->header('供应商')
            ->description('产品列表')
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
            ->header('产品原型')
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
        $grid->type('产品分类')->display(function ($v){
            return GoodsCate::find($v)->name;
        })->label();
        $grid->title('标题');
        $grid->cgy('采购员')->display(function ($v){
            return Cgy::find($v)['name'];
        });
        $grid->image('图片')->display(function ($v){
            $str = '';
            foreach (explode(',',$v) as $item){
                $str.= '<a target="_blank" href="'.env('APP_URL').$item.'"><img src="'.env('APP_URL').$item.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            }
            return $str;
        });
        $grid->gys_id('供应商')->display(function ($v){
            return Gys::find($v)->name;
        })->label();
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');
        $grid->is_pass('状态')->radio([
            0=>'审核中',
            1=>'审核通过'
        ]);
        $grid->quickSearch('title');
//        $grid->disableActions();
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('title', '产品标题');
            $filter->equal('gys_id','供应商')->select('/tenancy/getGysList');

        });
        $grid->disableCreateButton();
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

        $form->select('type', '产品分类')->options('/tenancy/getCateList');
        $form->text('title', '标题');
//        $form->decimal('gys_price', 'Gys price');
        $form->text('image', 'Image');
        $form->select('gys_id', 'Gys id')->options('/tenancy/getGysList');
        $form->radio('is_pass', 'Is pass')->options([0=>'审核中',1=>'审核通过']);

        return $form;
    }
}
