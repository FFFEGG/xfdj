<?php

namespace App\Tenancy\Controllers;

use App\CouponCode;
use App\User;
use App\UserCoupon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserCouponController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户优惠券列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserCoupon);
        $grid->model()->orderBy('created_at','desc');
        $grid->column('id', __('Id'));
        $grid->column('u_id', __('用户'))->display(function ($v){
            return User::find($v)->avatar;
        })->image(50,50);
        $grid->column('coupon_id', __('优惠券'))->display(function ($v){
            return CouponCode::find($v)->name;
        })->label();
        $grid->column('is_used', __('是否使用'))->display(function ($v){
            return $v? '是':'否';
        });
        $grid->column('created_at', __('创建时间'));

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
        $show = new Show(UserCoupon::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('u_id', __('U id'));
        $show->field('coupon_id', __('Coupon id'));
        $show->field('is_used', __('Is used'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserCoupon);
        $user = User::get();
        foreach ($user as $v) {
            $userarr[$v->id] = $v->nickname;
        }
        $coupon = CouponCode::get();
        foreach ($coupon as $v) {
            $couponarr[$v->id] = $v->name;
        }
        $form->select('u_id', __('用户昵称'))->options($userarr);
        $form->select('coupon_id', __('优惠券'))->options($couponarr);

        return $form;
    }
}
