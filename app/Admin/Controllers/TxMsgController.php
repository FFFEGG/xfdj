<?php

namespace App\Admin\Controllers;

use App\TxMsg;
use App\Http\Controllers\Controller;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TxMsgController extends Controller
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
            ->header('提现')
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
        $grid = new Grid(new TxMsg);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->u_id('用户')->display(function ($v){
            return '<img  src="'.User::find($v)['avatar'].'" style="width:50px;padding:5px;border:1px solid #eee;border-radius:3px" />';
        });
        $grid->price('提现金额');
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->banknum('银行卡号');
        $grid->khh('开户行');
        $grid->khzh('开户支行');
        $grid->is_pass('是否通过')->radio([
            0=> '审核中',
            1=> '已提现'
        ]);
        $grid->created_at('提交时间');
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
        $show = new Show(TxMsg::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->price('Price');
        $show->name('Name');
        $show->tel('Tel');
        $show->banknum('Banknum');
        $show->khh('Khh');
        $show->khzh('Khzh');
        $show->is_pass('Is pass');
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
        $form = new Form(new TxMsg);

        $form->number('u_id', 'U id');
        $form->decimal('price', 'Price');
        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->text('banknum', 'Banknum');
        $form->text('khh', 'Khh');
        $form->text('khzh', 'Khzh');
        $form->switch('is_pass', 'Is pass');

        return $form;
    }
}
