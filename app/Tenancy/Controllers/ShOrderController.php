<?php

namespace App\Tenancy\Controllers;

use App\ShOrder;
use App\Http\Controllers\Controller;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

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
        $grid->id('Id');
        $grid->u_id('用户')->display(function ($v){
            return User::find($v)->avatar;
        })->image(50,50);
        $grid->status('状态')->display(function ($v){
            return $v?'审核通过':'审核中';
        });
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->address('地址');
        $grid->created_at('申请时间');
        $grid->column('cz','操作')->display(function ($v) {
            if ($this->status == 0) {
                return '<button><a href="/tenancy/shordersh?id='.$this->id.'">审核</a></button>';
            } else {
                return '审核通过';
            }

        });
        $grid->disableActions();
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->scope('male', '审核中')->where('status', 0);
            $filter->scope('malse', '审核通过')->where('status','!=',0);


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
}
