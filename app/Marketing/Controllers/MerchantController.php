<?php

namespace App\Marketing\Controllers;

use App\Merchant;
use App\Http\Controllers\Controller;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MerchantController extends Controller
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
            ->header('商户审核')
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
        $content->row('<a href="http://api.map.baidu.com/lbsapi/getpoint/index.html" target="_blank">点击获取经纬度</a>');
        return $content
            ->header('商户')
            ->description('审核操作')
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
        $grid = new Grid(new Merchant);

        $grid->id('Id');
        $grid->u_id('用户')->display(function ($v){
            return User::find($v)['avatar'];
        })->image(50,50);
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->shopname('店铺名称');
        $grid->xqname('小区名称');
        $grid->address('详细地址');
        $grid->sfzZ('身份证正面')->image();
        $grid->sfzF('身份证反面')->image();
        $grid->sfzSC('身份证手持')->image();
        $grid->yyzz('营业执照')->image();
        $grid->status('状态')->display(function ($v){
            return $v?'审核通过':'审核中';
        });
        $grid->created_at('申请时间');
        $grid->column('caozuo','操作')->display(function (){
           return '<a href="/marketing/sh/'.$this->id.'/edit">审核</a>';
        });
        $grid->disableActions();
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
        $show = new Show(Merchant::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->name('Name');
        $show->tel('Tel');
        $show->shopname('Shopname');
        $show->xqname('Xqname');
        $show->address('Address');
        $show->yyzz('Yyzz');
        $show->longitude('Longitude');
        $show->latitude('Latitude');
        $show->status('Status');
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
        $form = new Form(new Merchant);

        $form->hidden('u_id','u_id');
        $form->text('name', '姓名');
        $form->text('tel', '电话');
        $form->text('shopname', '店铺名称');
        $form->text('xqname', '小区名称');
        $form->text('address', '地址');
        $form->image('yyzz', '营业执照');
        $form->text('longitude', '经度')->rules('required');
        $form->text('latitude', '纬度')->rules('required');
        $form->radio('status', '状态')->options([0=>'审核中',1=>'审核通过']);

        $form->saved(function (Form $form) {
            $user = User::find($form->u_id);
            if ($form->status == 0) {
                $user->is_merchant = false;
            } else {
                $user->is_merchant = true;
            }
            $user->save();
        });
        return $form;
    }
}
