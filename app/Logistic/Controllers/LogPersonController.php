<?php

namespace App\Logistic\Controllers;

use App\Logistic\Actions\Post\BatchReplicate;
use App\LogPerson;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class LogPersonController extends Controller
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
            ->header('物流人员')
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
            ->header('物流人员')
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
            ->header('物流人员')
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
        $grid = new Grid(new LogPerson);

        $grid->id('Id');
        $grid->pwd('登录口令');
        $grid->name('姓名');
        $grid->tel('联系电话');
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchReplicate());
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
        $show = new Show(LogPerson::findOrFail($id));

        $show->id('Id');
        $show->pwd('Pwd');
        $show->name('Name');
        $show->tel('Tel');
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
        $form = new Form(new LogPerson);

        $form->text('pwd', '请填写8位登录口令')->rules('unique:log_people|min:8|max:8');
        $form->text('name', '姓名');
        $form->text('tel', '电话');

        return $form;
    }
}
