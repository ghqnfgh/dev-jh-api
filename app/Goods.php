<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
	protected $table = "gd_goods";
	protected $primaryKey = "goodsno";
	public $timestamps = false;
    //
}
