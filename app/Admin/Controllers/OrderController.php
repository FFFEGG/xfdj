<?php

namespace App\Admin\Controllers;

use App\Group;
use App\Logistic\Extensions\PostsExporter;
use App\Order;
use App\OrderMsg;
use App\PsOrder;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->column('sn','交易号')->display(function ($v){
            return $v.$this->id;
        })->width(50);
        $grid->u_id('用户')->display(function ($v){
            return '<img  src="'.User::find($v)['avatar'].'" style="width:50px;padding:5px;border:1px solid #eee;border-radius:3px" />';
        });

        $grid->name('收货人姓名')->label();
        $grid->tel('电话')->label();
        $grid->column('cp','产品')->display(function ($v){
            return OrderMsg::where('order_id',$this->id)->first()->goods->title;
        })->width(100);
        $grid->column('ls','数量')->display(function ($v){
            return OrderMsg::where('order_id',$this->id)->first()->num;
        })->label();
        $grid->price('订单价格')->editable();
        $grid->status('订单状态')->radio([
            0=>'未付款',
            1=>'已付款，待发货',
            2=>'配送中',
            3=>'待提货',
            4=>'已提货',
            -1=>'取消订单',
        ]);
        $grid->paid_at('支付时间')->label();
        $grid->closed('交易状态')->display(function ($v){
            return $v==0? '交易中':'已关闭';
        })->label();
        $grid->created_at('创建时间')->sortable();
        $grid->ps_time('配送时间')->sortable();
        $grid->column('psorder.loguser','配送员')->display(function ($v){
            return PsOrder::whereOrderId($this->id)->first()?PsOrder::whereOrderId($this->id)->first()->loguser->name:'';
        })->label();
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
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableExport(false);
        $grid->exporter(new PostsExporter());
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

        $show->field('id', __('Id'));
        $show->field('sn', __('Sn'));
        $show->field('u_id', __('U id'));
        $show->field('group_id', __('Group id'));
        $show->field('name', __('Name'));
        $show->field('tel', __('Tel'));
        $show->field('msg', __('Msg'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('status', __('Status'));
        $show->field('remark', __('Remark'));
        $show->field('paid_at', __('Paid at'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_no', __('Payment no'));
        $show->field('refund_status', __('Refund status'));
        $show->field('refund_no', __('Refund no'));
        $show->field('extra', __('Extra'));
        $show->field('closed', __('Closed'));
        $show->field('formid', __('Formid'));
        $show->field('ps_time', __('Ps time'));
        $show->field('leader_id', __('Leader id'));
        $show->field('hd_time', __('Hd time'));
        $show->field('coupon_id', __('Coupon id'));

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

        $form->text('sn', __('Sn'));
        $form->number('u_id', __('U id'));
        $form->number('group_id', __('Group id'));
        $form->text('name', __('Name'));
        $form->text('tel', __('Tel'));
        $form->text('msg', __('Msg'));
        $form->decimal('price', __('Price'));
        $form->number('status', __('Status'));
        $form->textarea('remark', __('Remark'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('refund_status', __('Refund status'));
        $form->text('refund_no', __('Refund no'));
        $form->textarea('extra', __('Extra'));
        $form->switch('closed', __('Closed'));
        $form->text('formid', __('Formid'));
        $form->datetime('ps_time', __('Ps time'))->default(date('Y-m-d H:i:s'));
        $form->number('leader_id', __('Leader id'));
        $form->datetime('hd_time', __('Hd time'))->default(date('Y-m-d H:i:s'));
        $form->number('coupon_id', __('Coupon id'));

        return $form;
    }
}
