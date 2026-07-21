<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Model;
	
	class AttributeGroups extends Model
	{
		protected $table = 'attribute_groups';
		
		public function meta()
		{
			return $this->hasMany(AttributeGroupDescription::class, 'attribute_group_id');
		}
		
		public function metaLang()
		{
			return $this->hasMany(AttributeGroupDescription::class, 'attribute_group_id')->where('lang', config('app.locale'));
		}
	}
