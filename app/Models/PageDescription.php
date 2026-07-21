<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageDescription extends Model
{
    protected $table = 'page_description';

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
