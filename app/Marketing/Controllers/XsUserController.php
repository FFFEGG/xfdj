<?php

namespace App\Marketing\Controllers;

use App\User;
use App\XsUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

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
        $grid->img('照片')->image(env('APP_URL').'/uploads/',50,50);
        $grid->tc('提成')->sortable();
        $grid->column('thd', 'ta的提货点')->display(function () {
            return '点击查看' . User::where('p_id', $this->u_id)->orderby('created_at', 'desc')->where('user_type', 3)->get()->count();
        })->expand(function ($model) {

            $comments = User::where('p_id', $this->u_id)->orderby('created_at', 'desc')->where('user_type', 3)->get()->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'avatar' => '<img src="' . $comment->avatar . ' " width=30>',
                    'nickname' => $comment->nickname,
                    'zprice' => $comment->zmoney,
                    'created_at' => $comment->created_at,
                ];
            });

            return new Table(['ID', '头像', '昵称', '收益', '注册时间'], $comments->toArray());
        });


        $grid->column('tdzt', 'ta的社区代理')->display(function () {
            return '点击查看' . User::where('p_id', $this->u_id)->orderby('created_at', 'desc')->where('user_type', 1)->get()->count();
        })->expand(function ($model) {

            $commentss = User::where('p_id', $this->u_id)->orderby('created_at', 'desc')->where('user_type', 1)->get()->map(function ($v) {
                return [
                    'id' => $v->id,
                    'avatar' => '<img src="' . $v->avatar . ' " width=30>',
                    'nickname' => $v->nickname,
                    'zprice' => $v->zmoney,
                    'created_at' => $v->created_at,
                ];
            });

            return new Table(['ID', '头像', '昵称', '收益', '注册时间'], $commentss->toArray());
        });
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

        $list = User::whereUserType(2)->get();

        foreach ($list as $k=>$v) {
            $arr[$v->id] = $v->nickname;
        }


        $form->select('u_id', '微信用户')->options($arr);
        $form->text('name', '姓名');
        $form->text('tel', '电话');
        $form->text('address', '地址');
        $form->image('img', '照片')->uniqueName();
        $form->decimal('tc', '提成')->default(0.01);

        return $form;
    }
}
