<?php

namespace App\Logistic\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchReplicate extends BatchAction
{
    public $name = '批量复制';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            // ...
            echo 1;
        }

        return $this->response()->success('Success message...')->refresh();
    }


}
