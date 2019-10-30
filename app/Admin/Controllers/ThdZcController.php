<?php

namespace App\Admin\Controllers;

use App\ThdZc;
use App\Http\Controllers\Controller;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ThdZcController extends Controller
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
            ->header('Index')
            ->description('description')
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
        $grid = new Grid(new ThdZc);
        $grid->model()->orderBy('created_at','desc');
        $grid->id('Id');
        $grid->u_id('用户')->display(function (){
            return '<img src="'.User::find($this->u_id)['avatar'].'" width=50/>';
        });
        $grid->name('姓名');
        $grid->tel('电话');
        $grid->shopname('店铺名称');
        $grid->xqname('小区名称');
        $grid->address('地址');
        $grid->sfzz('身份证正面')->image(300,100);
        $grid->sfzf('身份证反面')->image(300,100);
        $grid->sfzsc('手持身份证')->image(300,100);
        $grid->yyzz('营业执照')->image(300,100);
        $grid->is_sh('是否审核通过')->display(function ($v){
            if ($v == 1) {
                return '通过';
            }
            if ($v == -1) {
                return '不通过';
            }
            return '<a href="/xfdj_admin/thd_pass?id='.$this->u_id.'&msg_id='.$this->id.'"><button class="btn-primary">是</button></a>'
                .'<br/><a href="/xfdj_admin/thd_close?id='.$this->u_id.'&msg_id='.$this->id.'"><button class="btn-reddit">否</button></a>';
        });
        $grid->updated_at('修改时间');
        $grid->created_at('申请时间');
//        $grid->disableActions();


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
        $show = new Show(ThdZc::findOrFail($id));

        $show->id('Id');
        $show->u_id('U id');
        $show->name('Name');
        $show->tel('Tel');
        $show->sfzz('Sfzz');
        $show->sfzf('Sfzf');
        $show->sfzsc('Sfzsc');
        $show->is_sh('Is sh');
        $show->updated_at('Updated at');
        $show->created_at('Created at');
        $show->yyzz('Yyzz');
        $show->shopname('Shopname');
        $show->xqname('Xqname');
        $show->address('Address');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ThdZc);

        $form->number('u_id', 'U id');
        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->text('sfzz', 'Sfzz');
        $form->text('sfzf', 'Sfzf');
        $form->text('sfzsc', 'Sfzsc');
        $form->switch('is_sh', 'Is sh');
        $form->text('yyzz', 'Yyzz');
        $form->text('shopname', 'Shopname');
        $form->text('xqname', 'Xqname');
        $form->text('address', 'Address');

        return $form;
    }
}
