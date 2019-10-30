<?php

namespace App\Logistic\Controllers;

use App\ShOrder;
use App\Http\Controllers\Controller;
use App\ShPsOrder;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class ShOrderController extends Controller
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
            ->header('社区商户订单审核')
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
        $grid = new Grid(new ShOrder);
        $grid->model()->orderBy('created_at','desc');
        $grid->model()->whereIn('status',[1,2,3]);
        $grid->id('Id');
        $grid->u_id('用户')->display(function ($v){
            return User::find($v)->avatar;
        })->image(50,50);
        $grid->status('状态')->display(function ($v){
            switch ($v) {
                case 1:
                    return '待配送';
                    break;
                case 2:
                    return '配送中';
                    break;
                case 3:
                    return '配送完成';
                    break;
            }
        });
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->address('地址');
        $grid->created_at('申请时间');
        $grid->column('psy','配送员')->display(function ($v){
            return ShPsOrder::whereOrderId($this->id)->first()?ShPsOrder::whereOrderId($this->id)->first()->loguser->name:'';
        })->label();
        $grid->column('cz','操作')->display(function ($v) {
            if ($this->status == 1) {
                return '<button><a href="/logistic/shorderps?id='.$this->id.'">点击配送</a></button>';
            } else {
                return '';
            }

        });
        $grid->disableActions();
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->scope('maless', '待配送')->where('status', 1);
            $filter->scope('male', '配送中')->where('status', 2);
            $filter->scope('malse', '配送完成')->where('status',3);


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
        $show = new Show(ShOrder::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->status('Status');
        $show->name('Name');
        $show->tel('Tel');
        $show->address('Address');
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
        $form = new Form(new ShOrder);

        $form->number('u_id', 'U id');
        $form->number('status', 'Status');
        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->text('address', 'Address');

        return $form;
    }


    public function shorderps(Request $request,Content $content)
    {

        if($request->isMethod('post')) {
            ShPsOrder::create([
                'u_id' => $request->u_id,
                'order_id' =>$request->id
            ]);

            $order = ShOrder::find($request->id);
            $order->status = 2;
            $order->save();
            $success = new \Illuminate\Support\MessageBag([
                'title'   => '操作成功',
                'message' => '',
            ]);
            return redirect('/logistic/shorder')->with(compact('success'));

        }

        $order = \App\ShOrder::with(['msg','msg.goods'])->whereId($request->id)->first();
        return $content
            ->header('商户订单配送')
            ->body(view('logistic.shorderps',compact('order')));
    }
}
