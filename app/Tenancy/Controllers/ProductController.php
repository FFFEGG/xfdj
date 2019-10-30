<?php

namespace App\Tenancy\Controllers;

use App\Cgy;
use App\CouponCode;
use App\GoodsCate;
use App\Product;
use App\Http\Controllers\Controller;
use App\ProductType;
use App\Region;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;

class ProductController extends Controller
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
            ->header('产品')
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
            ->header('产品')
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
            ->header('产品')
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
        $sy_type = [
            'on'  => ['value' => 0, 'text' => '百分比', 'color' => 'success'],
            'off' => ['value' => 1, 'text' => '元/每件', 'color' => 'default'],
        ];
        $grid = new Grid(new Product);
        $grid->model()->orderBy('sort','asc')->orderBy('created_at','desc');
        $grid->header(function ($query) {
            $gender = [
                'star' =>Product::where('star_time','<=',date('Y-m-d H:i:s',time()))
                    ->where('end_time','>=',date('Y-m-d H:i:s',time()))
                    ->count(),
                'yg' =>Product::where('is_yg',true)->count(),
                'end' =>Product::where('end_time','<=',date('Y-m-d H:i:s',time()))->count(),
            ];
            $doughnut = view('admin.product.gender', compact('gender'));
            $row = new Row();
            $row->column(12,new Box('产品分布概况', $doughnut));
            return $row;
        });
        $grid->id('Id');
        $grid->cate_id('产品分类')->display(function ($v){
            return GoodsCate::find($v)['name'];
        })->label('primary');
//        $grid->column('pics','产品图片')->carousel($width = 150, $height = 80, env('APP_URL').'/uploads');
        $grid->column('pics','产品图片')->display(function ($v){
            return $v[0];
        })->image(env('APP_URL').'/uploads',50,50);
        $grid->price('价格')->display(function ($v){
            return '￥'.$v;
        })->setAttributes(['style' => 'color:red;'])->sortable();
        $grid->title('产品标题')->editable();

        $grid->real_sales('实际销量')->sortable();
        $grid->sales_num('销量')->editable();
        $grid->status('状态')->display(function ($v){
            if ($v == 2) {
                return '即将开团';
            }
            if ($v == 1) {
                return '开团中';
            }

            if ($v == 3) {
                return '开团结束';
            }

        })->label();
//        $grid->sy_type('收益方式')->switch($sy_type);
//        $grid->leader_sy('社区代理收益')->editable();
//        $grid->group_sy('提货点收益')->display(function ($v){
//            return '￥'.$v.'元/件';
//        });
//        $grid->old_price('市场价')->editable();
        $grid->stock('库存')->editable();
        $grid->column('url','小程序链接')->display(function ($v){
            return '/pages/goods?id='.$this->id;
        })->label();
//        $grid->sales_num('销量')->editable();
//        $grid->ps_time('配送时间')->editable();
//        $grid->star_time('开始时间')->label()->sortable();
        $grid->end_time('结束时间')->editable('datetime');
//        $grid->content('Content');
        $grid->is_yg('是否加入预告')->switch($states);
        $grid->is_sj('是否上架')->switch($states);
        $grid->is_tj('是否推荐')->switch($states);
        $grid->is_shop('可进货')->switch($states);
        $grid->is_xg('限购')->switch($states);
        $grid->xg_num('限购数量')->editable();
        $grid->sort('排序')->editable();
//        $grid->quickSearch('title');
//        $grid->created_at('创建时间');
//        $grid->updated_at('更新时间');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('cate_id','产品分类')->select('/admin/getCateList');
            // 在这里添加字段过滤器
            $filter->like('title', '产品标题');
            // 设置datetime类型
            $filter->between('end_time', '结束时间')->datetime();
        });
//        $grid->fixColumns(5, -8);
        $grid->enableHotKeys();
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
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->cate_id('Cate id');
        $show->title('Title');
        $show->price('Price');
        $show->old_price('Old price');
        $show->pics('Pics');
        $show->stock('Stock');
        $show->sales_num('Sales num');
        $show->ps_time('Ps time');
        $show->end_time('End time');
        $show->content('Content');
        $show->is_mj('Is mj');
        $show->is_xg('Is xg');
        $show->xg_num('Xg num');
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
        $states = [
            'on'  => ['value' => true, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => false, 'text' => '否', 'color' => 'danger'],
        ];

        $sy_type = [
            'on'  => ['value' => 0, 'text' => '百分比', 'color' => 'success'],
            'off' => ['value' => 1, 'text' => '元/每件', 'color' => 'default'],
        ];

        $form = new Form(new Product);
//        $form->tab('分类选择', function ($form) {
//
//        });

        $form->tab('必填项', function ($form) use ($sy_type) {
            $form->select('cate_id', '产品分类')->options('/tenancy/getCateList')->required();
            $form->select('gys_id', '供应商')->options('/tenancy/getGysList')->required();
            $form->text('title', '产品标题')->required();
            $form->multipleImage('pics', '产品图片')->removable()->uniqueName()->help('可一次框选多张图片')->sortable();
            $form->image('fx_img', '分享图片')->uniqueName();
            $form->currency('price', '产品价格')->required()->symbol('￥');
            $form->currency('old_price', '市场价')->required()->symbol('￥');
            $form->multipleSelect('regions','上架区域')->options(function (){
                $data = Region::get();
                foreach ($data as $v) {
                    $arr[$v->id] = $v->name;
                }
                return $arr;
            })->required();
            $form->select('cgy_id','采购员')->options(function (){
                $data = Cgy::get();
                foreach ($data as $v) {
                    $arr[$v->id] = $v->name;
                }
                return $arr;
            })->required();
            $form->select('type','产品类型')->options(function (){
                $data = ProductType::get();
                foreach ($data as $v) {
                    $arr[$v->id] = $v->name;
                }
                return $arr;
            })->required();
//            $form->checkbox('type','产品类型')->options(ProductType::all()->pluck('name', 'id'));
            $form->text('min_qs', '最低售卖件数')->required()->default(1);
//            $form->checkbox('type','类型')->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);
            $form->switch('sy_type', '社区代理收益结算方式')->states($sy_type)->help('此项只针对社区代理');
            $form->decimal('leader_sy', '社区代理收益')->required()->help('百分比请填小数 例如：0.01，元/每件请填整数');
            $form->currency('group_sy', '提货点收益')->required()->symbol('￥');
            $form->datetimeRange('star_time','end_time', '拼团时间')->required();
            $form->datetime('ps_time', '货到时间')->required()->help('货到时间注意要比拼团时间晚')->format('YYYY-MM-DD HH');
            $form->slider('stock', '库存')->options(['max' => 999, 'min' => 1, 'step' => 1, 'postfix' => '件'])->default(999)->required();
            $form->slider('sort','排序')->options(['max' => 100, 'min' => 1, 'step' => 1, 'postfix' => ''])->default(50)->required();
            $form->UEditor('content', '产品详情')->required();


        });

        $form->tab('选填项', function ($form) use ($states) {
            $form->hasMany('specs', '规格', function (Form\NestedForm $form) {
                $form->text('name','规格值');
                $form->image('thumb','图片')->uniqueName();
                $form->currency('price', '价格')->required()->symbol('￥');
            });
            $form->multipleSelect('coupons','可使用优惠券')->options(function (){
                $data = CouponCode::get();
                foreach ($data as $v) {
                    $arr[$v->id] = $v->name;
                }
                return $arr;
            });
            $form->hidden('sales_num', '销量')->default(rand(9,30));
            $form->switch('is_yg', '是否加入预告')->states($states);
            $form->switch('is_tj', '是否推荐位')->states($states)->default(false);
            $form->switch('is_sj', '是否上架')->states($states)->default(true);
            $form->switch('is_shop', '是否可进货')->states($states)->default(false);
            $form->switch('is_mj', '是否参加满减活动')->states($states);
            $form->switch('is_xg', '是否参加限购活动')->states($states);
            $form->number('xg_num', '限购数量')->default(0);
            $form->radio('status','开团状态')->options([1=>'开团中',2=>'即将开团',3=>'团结束']);
        });

        return $form;
    }
}
