<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroups extends Model
{
    protected $table = 'customer_groups';

    public function meta()
    {
        return $this->hasMany(CustomerGroupDescription::class, 'customer_group_id');
    }
}
