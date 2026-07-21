<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class CustomerSocial extends Model
	{
		protected $table = 'customer_social';
		
		protected $casts = [
			'social' => 'array'
		];
	}
