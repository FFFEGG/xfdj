<?php

namespace App\Admin\Controllers;

use App\User;
use App\XsUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class XsUserController extends Controller
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
            ->header('销售员')
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
            ->header('销售员')
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
            ->header('销售员')
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
        $grid = new Grid(new XsUser);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->u_id('微信用户')->display(function ($v){
            return User::find($v)['nickname'];
        });
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->address('地址');
        $grid->img('照片')->image();
        $grid->tc('提成')->sortable();
        $grid->created_at('创建时间');
        $grid->updated_at('更改时间');

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
        $show = new Show(XsUser::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->name('Name');
        $show->tel('Tel');
        $show->address('Address');
        $show->img('Img');
        $show->tc('Tc');
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
        $form = new Form(new XsUser);

        $form->select('u_id', '微信用户')->options(function ($id) {
            $user = User::where('openid',$id)->first();

            if ($user) {
                return [$user->id => $user->nickname];
            }
        })->ajax('/admin/api/users');
        $form->text('name', '姓名');
        $form->text('tel', '电话');
        $form->text('address', '地址');
        $form->image('img', '照片')->uniqueName();
        $form->decimal('tc', '提成')->default(0.01);

        return $form;
    }
}
