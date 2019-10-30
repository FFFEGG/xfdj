<?php

namespace App\Marketing\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;

class shUserController extends Controller
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
            ->header('社区商户')
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
        $grid = new Grid(new User);
        $grid->model()->orderBy('created_at','desc');
        $grid->model()->where('is_merchant',true);
        $grid->id('Id');
        $grid->nickname('昵称');
        $grid->gender('性别')->using([0 => '女', 1 => '男'])->label();
        $grid->avatar('头像')->image(50,50);
        $grid->openid('Openid')->label();
        $grid->is_merchant('是否是社区商户')->switch();
        $grid->f_id('上级')->display(function ($v){
            return User::find($v)['avatar'];
        })->image(50,50);

        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->filter(function($filter){
            // 在这里添加字段过滤器
            $filter->like('nickname', '昵称');
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
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->nickname('Nickname');
        $show->gender('Gender');
        $show->avatar('Avatar');
        $show->phone('Phone');
        $show->openid('Openid');
        $show->formid('Formid');
        $show->p_id('P id');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->user_type('User type');
        $show->is_sh('Is sh');
        $show->money('Money');
        $show->zmoney('Zmoney');
        $show->f_id('F id');
        $show->is_merchant('Is merchant');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('nickname', 'Nickname')->default('幸福到家会员');
        $form->number('gender', 'Gender');
        $form->image('avatar', 'Avatar')->default('https://wx.qlogo.cn/mmhead/Q3auHgzwzM4h4b2KibAP4PYyzBjeQLBghOzw6HZlX1VhoFFTIpC32Aw/0');
        $form->mobile('phone', 'Phone');
        $form->text('openid', 'Openid');
        $form->text('formid', 'Formid');
        $form->number('p_id', 'P id');
        $form->number('user_type', 'User type');
        $form->switch('is_sh', 'Is sh');
        $form->decimal('money', 'Money')->default(0.00);
        $form->decimal('zmoney', 'Zmoney')->default(0.00);
        $form->number('f_id', 'F id');
        $form->switch('is_merchant', 'Is merchant');

        return $form;
    }
}
