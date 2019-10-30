<?php

namespace App\Tenancy\Controllers;

use App\Group;
use App\Logistic\Extensions\PostsExporter;
use App\Order;
use App\Http\Controllers\Controller;
use App\OrderMsg;
use App\Product;
use App\PsList;
use App\PsOrder;
use App\User;
use App\XsUser;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class OrderController extends Controller
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
            ->header('订单列表')
            ->description('')
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
        $grid = new Grid(new Order);
        $grid->model()->orderBy('created_at','desc');
        $grid->model()->whereIn('status',[1,2,3,4])->where('paid_at','!=','');
        $grid->id('Id');
        $grid->sn('交易号')->display(function ($v){
            return $v.$this->id;
        });
        $grid->u_id('用户')->display(function ($v){
            return '<img  src="'.User::find($v)['avatar'].'" style="width:50px;padding:5px;border:1px solid #eee;border-radius:3px" />';
        });
        $grid->column('tuanzhang','团长')->display(function ($v){
            return User::find($this->leader_id)['nickname'];
        });
        $grid->column('xxy','销售员')->display(function ($v){
            return XsUser::whereUId(User::find($this->leader_id)['p_id'])->first()['name'];
        });
        $grid->group_id('取货点')->display(function ($v){
            return Group::find($v)->title;
        })->label()->sortable();
        $grid->column('xhdtel','取货点电话')->display(function ($v){
            return Group::find($this->group_id)->tel;
        });

        $grid->name('收货人姓名')->label();

        $grid->tel('电话')->label();

        $grid->column('cp','产品')->display(function ($v){
            return OrderMsg::where('order_id',$this->id)->first()->goods->title.OrderMsg::where('order_id',$this->id)->first()->spec;
        });

        $grid->column('ls','数量')->display(function ($v){
            return OrderMsg::where('order_id',$this->id)->first()->num;
        })->label();

        $grid->msg('留言')->label();

        $grid->price('订单价格')->sortable();

//        $grid->remark('订单备注');
        $grid->paid_at('支付时间')->label();
//        $grid->payment_method('支付方式');
//        $grid->payment_no('支付平台订单号');
//        $grid->refund_status('退款状态');
//        $grid->refund_no('退款单号');
//        $grid->extra('Extra');
//        $grid->closed('交易状态')->display(function ($v){
//            return $v==0? '交易中':'已关闭';
//        })->label();
        $grid->created_at('创建时间')->sortable();

        $grid->ps_time('配送时间')->sortable();


        $grid->column('pslist.logps','(新)配送员')->display(function ($v){
            return PsList::whereOrderId($this->id)->first()?PsList::whereOrderId($this->id)->first()->logps->loguser->name:'';
        })->label();

        $grid->column('psorder.loguser','(旧)配送员')->display(function ($v){
            return PsOrder::whereOrderId($this->id)->first()?PsOrder::whereOrderId($this->id)->first()->loguser->name:'';
        })->label();

        $grid->status('订单状态')->display(function ($v){
            if ($v == 0) {
                return '未付款';
            }

            if ($v == 1) {
                return '已付款，待发货';
            }
            if ($v == 2) {
                return '配送中';
            }

            if ($v == 3) {
                return '待提货';
            }
            if ($v == 4) {
                return '已提货';
            }
        });
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 关联关系查询
            $filter->where(function ($query) {
                $query->whereHas('group', function ($query) {
                    $query->where('title', 'like', "%{$this->input}%")
                        ->orWhere('address', 'like', "%{$this->input}%")
                        ->orWhere('xqname', 'like', "%{$this->input}%");
                });
            },'社区名称，地址，小区名称');


            // 关联关系查询
            $filter->where(function ($query) {
                $query->whereHas('psorder.loguser', function ($query) {
                    $query->where('name', 'like', "%{$this->input}%");
                });
            },'配送员');
            $filter->between('ps_time', '配送时间')->datetime();
            $filter->between('paid_at', '支付时间')->datetime();
            $filter->scope('dfh', '待配送')->where('status', 1);
            $filter->scope('psz', '配送中')->where('status', 2);
            $filter->scope('pswc', '配送完成')->where('status', 3);
            $filter->scope('yth', '已提货')->where('status', 4);
            $filter->where(function ($query) {
                $q = substr($this->input,20,10);
                $query->where('id', 'like', "%{$q}%");

            }, '交易号');

        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->enableHotKeys();
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableExport(false);
        $grid->exporter(new \App\Tenancy\Extensions\PostsExporter());
        $grid->fixColumns(1, -1);
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
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
        $show->sn('Sn');
        $show->u_id('U id');
        $show->group_id('Group id');
        $show->name('Name');
        $show->tel('Tel');
        $show->msg('Msg');
        $show->price('Price');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->status('Status');
        $show->remark('Remark');
        $show->paid_at('Paid at');
        $show->payment_method('Payment method');
        $show->payment_no('Payment no');
        $show->refund_status('Refund status');
        $show->refund_no('Refund no');
        $show->extra('Extra');
        $show->closed('Closed');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('sn', 'Sn');
        $form->number('u_id', 'U id');
        $form->number('group_id', 'Group id');
        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->text('msg', 'Msg');
        $form->decimal('price', 'Price');
        $form->number('status', 'Status');
        $form->textarea('remark', 'Remark');
        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', 'Payment method');
        $form->text('payment_no', 'Payment no');
        $form->text('refund_status', 'Refund status');
        $form->text('refund_no', 'Refund no');
        $form->textarea('extra', 'Extra');
        $form->switch('closed', 'Closed');

        return $form;
    }
}
