<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageCategoryDescription extends Model
{
    protected $table = 'page_category_description';

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
