<?php

namespace App\Admin\Controllers;

use App\Gys;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GysController extends Controller
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
            ->header('供应商')
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
        $grid = new Grid(new Gys);

        $grid->id('Id');
        $grid->name('公司名称');
        $grid->tel('联系人电话');
        $grid->file('营业执照')->display(function ($v){
            $str = '';
            foreach (explode(',',$v) as $item){
                $str.= '<a target="_blank" href="'.env('APP_URL').$item.'"><img src="'.env('APP_URL').$item.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            }
            return $str;
        });
        $grid->hyzz('资质证书')->display(function ($v){
            $str = '';
            foreach (explode(',',$v) as $item){
                $str.= '<a target="_blank" href="'.env('APP_URL').$item.'"><img src="'.env('APP_URL').$item.'" width="100" style="padding:10px;border:solid #eee 1px;border-radius:3px;margin-right:3px"/></a>';
            }
            return $str;
        });
        $grid->type('类型');
        $grid->username('用户名称');
        $grid->password('密码');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        $grid->status('账户状态')->radio([
            0=> '审核中',
            1=> '审核通过',
            2=> '冻结账户'
        ]);

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
        $show = new Show(Gys::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->tel('Tel');
        $show->file('File');
        $show->type('Type');
        $show->username('Username');
        $show->password('Password');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Gys);

        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->textarea('file', 'File');
        $form->number('type', 'Type');
        $form->text('username', 'Username');
        $form->password('password', 'Password');
        $form->number('status', 'Status');
        // 在表单提交前调用
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });
        return $form;
    }
}
