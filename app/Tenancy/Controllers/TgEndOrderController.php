<?php

namespace App\Tenancy\Controllers;

use App\Cgy;
use App\Gys;
use App\Product;
use App\Tenancy\Extensions\TgOrder;
use App\TgEndOrder;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TgEndOrderController extends Controller
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
            ->header('订单数量-供应商')
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
        $grid = new Grid(new TgEndOrder);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->u_id('供应商')->display(function ($v){
            return Gys::find($v)->name;
        })->sortable();
        $grid->column('cgy','采购员')->display(function ($v){
            return Cgy::find(Product::find($this->goods_id)->cgy_id)['name'];
        })->label();

        $grid->column('tel','供应商电话')->display(function ($v){
            return Gys::find($this->u_id)->tel;
        });
        $grid->num('数量');
        $grid->goods_id('产品')->display(function ($v){
            return Product::find($v)->title;
        });
        $grid->column('pic','图片')->display(function ($v){
            return '<img src="/uploads/'.Product::find($this->goods_id)->pics[0].'" width=50/>';
        });
        $grid->spec('规格');
        $grid->status('状态')->display(function ($v){
            return $v==0?'未发货':'已发货';
        });
        $grid->end_time('拼团结束时间')->sortable();
        $grid->column('cz','操作')->display(function ($v){
            if ($this->status == 0) {
                return '<a href="/tenancy/tz?id='.$this->id.'"><button class="btn-primary">点击通知供应商发货</button></a>';
            } else {
                return '已通知';
            }

        });
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableExport(false);
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->between('end_time', '拼团结束时间')->datetime();
        });
        $grid->exporter(new TgOrder());
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
        $show = new Show(TgEndOrder::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->num('Num');
        $show->goods_id('Goods id');
        $show->status('Status');
        $show->end_time('End time');
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
        $form = new Form(new TgEndOrder);

        $form->number('u_id', 'U id');
        $form->number('num', 'Num');
        $form->number('goods_id', 'Goods id');
        $form->number('status', 'Status');
        $form->datetime('end_time', 'End time')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
