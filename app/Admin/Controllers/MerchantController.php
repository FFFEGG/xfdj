<?php

namespace App\Admin\Controllers;

use App\Merchant;
use App\Http\Controllers\Controller;
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
        $grid = new Grid(new Merchant);

        $grid->id('Id');
        $grid->u_id('U id');
        $grid->name('Name');
        $grid->tel('Tel');
        $grid->shopname('Shopname');
        $grid->xqname('Xqname');
        $grid->address('Address');
        $grid->yyzz('Yyzz');
        $grid->longitude('Longitude');
        $grid->latitude('Latitude');
        $grid->status('Status');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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

        $form->number('u_id', 'U id');
        $form->text('name', 'Name');
        $form->text('tel', 'Tel');
        $form->text('shopname', 'Shopname');
        $form->text('xqname', 'Xqname');
        $form->text('address', 'Address');
        $form->text('yyzz', 'Yyzz');
        $form->text('longitude', 'Longitude');
        $form->text('latitude', 'Latitude');
        $form->number('status', 'Status');

        return $form;
    }
}
