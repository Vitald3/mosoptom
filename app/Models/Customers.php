<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $table = 'customers';

    public function customer_group_description()
    {
        return $this->hasOne(CustomerGroupDescription::class, 'customer_group_id', 'customer_group_id')->where('lang', config('app.locale'));
    }

    public function getIp()
    {
        return $this->hasMany(CustomerIp::class, 'customer_id');
    }

    public function getOrders()
    {
        return $this->hasMany(Orders::class, 'customer_id');
    }

    public function getRewards()
    {
        return $this->hasMany(CustomerReward::class, 'customer_id');
    }
	
	public function address()    {
		return $this->hasMany(CustomerAddress::class, 'customer_id');
	}
	
	public function legal()    {
		return $this->hasOne(CustomerLegal::class, 'customer_id');
	}
	
	public function emails()    {
		return $this->hasMany(CustomerEmail::class, 'customer_id');
	}
	
	public function phones()    {
		return $this->hasMany(CustomerPhone::class, 'customer_id');
	}
	
	public function default_address()    {
		return $this->hasOne(CustomerAddress::class, 'id', 'address_id');
	}
	
	public function social()    {
		return $this->hasMany(CustomerSocial::class, 'customer_id');
	}
	
	public function wishlist()    {
		return $this->hasMany(CustomerWishlist::class, 'customer_id');
	}

    public static function getRewardPoints() {
        return CustomerReward::where('customer_id', session('customer_id'))->sum('points');
    }
}
