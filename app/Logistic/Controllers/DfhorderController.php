<?php

namespace App\Logistic\Controllers;

use App\Group;
use App\Logistic\Actions\Ps\BatchReplicate;
use App\Logistic\Extensions\PostsExporter;
use App\Msg;
use App\Order;
use App\Http\Controllers\Controller;
use App\OrderMsg;
use App\Product;
use App\PsOrder;
use App\User;
use App\Wechat;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class DfhorderController extends Controller
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
            ->header('待发货订单')
            ->description('配送中心')
            ->body($this->grid());
    }

    public function ps(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = 2;
        $order->ps_time = date('Y-m-d H:i:s',time());
        $order->save();
        $str = '';
        foreach (OrderMsg::where('order_id',$order->id)->get() as $v){
            $str.= Product::find($v->goods_id)['title'].'; ';
        }
        Wechat::msg()->template_message->send([
            'touser' => User::find($order->u_id)->openid,
            'template_id' => 'MBDMQDU1MjqJnVfn--_rwXDSwV7GxVzBD1H9n4ha-VQ',
            'page' => '/pages/orderdata?id='.$order->id,
            'form_id' => $order->formid,
            'data' => [
                'keyword1' => $order->ps_time,
                'keyword2' => $str,
                'keyword3' => '幸福到家物流',
                'keyword4' =>  Group::find($order->group_id)['title']. Group::find($order->group_id)['address'],
                'keyword5' =>  date("m月d日", strtotime("+1 day"))
            ],
        ]);
        $success = new MessageBag([
            'title'   => '操作成功',
        ]);
        return back()->with(compact('success'));
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
        $grid->sn('交易号');
        $grid->hd_time('到货时间')->label();
        $grid->ps_time('配送时间')->label();
        $grid->column('psorder.loguser','配送员')->display(function ($v){
            return PsOrder::whereOrderId($this->id)->first()?PsOrder::whereOrderId($this->id)->first()->loguser->name:'';
        })->label();
        $grid->column('cp','产品')->display(function ($v){
            return OrderMsg::where('order_id',$this->id)->first()->goods->title;
        });
        $grid->column('ls','数量')->display(function ($v){
            return OrderMsg::where('order_id',$this->id)->first()->num;
        })->label();
//        $grid->u_id('用户')->display(function ($v){
//            return '<img  src="'.User::find($v)['avatar'].'" style="width:50px;padding:5px;border:1px solid #eee;border-radius:3px" />';
//        });
        $grid->group_id('取货点')->display(function ($v){
            return Group::find($v)->title;
        })->label();
//        $grid->name('收货人姓名')->label();
//        $grid->tel('电话')->label();
//        $grid->msg('留言')->label();
//        $grid->price('订单价格');
        $grid->status('订单状态')->display(function ($v){
            if ($v == 1) {
                return '待配送';
            }
            if ($v == 2) {
                return '配送中';
            }
            if ($v == 3) {
                return '配送完成';
            }
            if ($v == 4) {
                return '已提货';
            }
        });
        $grid->paid_at('支付时间')->label();

        $grid->column('fh','操作')->display(function ($v){
            if ($this->status ==1) {
                return '<a href="/logistic/order_ps?id='.$this->id.'"><button>确认配送</button></a>';
            }
        });
        $grid->column('dy','打印')->display(function (){
            return '<a href="/logistic/dy?id='.$this->id.'"><button>打印</button></a>';
        });
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableExport(false);
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
            $filter->between('hd_time', '到货时间')->datetime();
        });
//        $grid->exporter(new PostsExporter());
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new BatchReplicate());
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


    public function order_ps(Request $request,Content $content)
    {

        if ($request->isMethod('post')) {
            $order = Order::find($request->id);
            $order->status = 2;
            $order->ps_time = date('Y-m-d H:i:s',time());
            $order->save();

            PsOrder::create([
                'u_id' => $request->u_id,
                'order_id' => $request->id,
            ]);

            $str = $order->items[0]->goods->title;

            Msg::sendmsg(1,$order->tel,$str);

            $success = new MessageBag([
                'title'   => '操作成功',
                'message' => '',
            ]);

            return redirect('/logistic/orders')->with(compact('success'));

        }

        $order = Order::find($request->id);
        return $content
            ->header('订单配送')
            ->body(view('logistic.order_ps',compact('order')));
    }
}
