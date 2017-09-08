<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

	protected $table = 'gd_category';
	
/*	public function parents(){
		return $this->belongsTo(self::class)->where('category',"LIKE",'category','%');
	}
	public function children(){
		return $this->hasMany(self::class, 'category');
	}*/
	
    //
}
