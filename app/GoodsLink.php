<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsLink extends Model
{
	protected $table = 'gd_goods_link';
	protected $primaryKey = 'sno';
	protected $casts = [
		'category' => 'string'
	];
	
    //
}
