<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
	protected $table = 'gd_category';
	protected $primaryKey = 'category';
	protected $casts = [
		'category' => 'string'
	];
    //
}
