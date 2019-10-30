<?php

namespace App\Admin\Controllers;

use App\Restaurant;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RestaurantController extends Controller
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
        $grid = new Grid(new Restaurant);

        $grid->id('Id');
        $grid->qsj('Qsj');
        $grid->psf('Psf');
        $grid->longitude('Longitude');
        $grid->latitude('Latitude');
        $grid->title('Title');
        $grid->thumb('Thumb');
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
        $show = new Show(Restaurant::findOrFail($id));

        $show->id('Id');
        $show->qsj('Qsj');
        $show->psf('Psf');
        $show->longitude('Longitude');
        $show->latitude('Latitude');
        $show->title('Title');
        $show->thumb('Thumb');
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
        $form = new Form(new Restaurant);

        $form->decimal('qsj', 'Qsj');
        $form->decimal('psf', 'Psf');
        $form->text('longitude', 'Longitude');
        $form->text('latitude', 'Latitude');
        $form->text('title', 'Title');
        $form->text('thumb', 'Thumb');

        return $form;
    }
}
