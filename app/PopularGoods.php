<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PopularGoods extends Model
{
	
	protected $table = "gd_goods_display";
	protected $primaryKey = "no";
	
	public $timestamps = false;

	public function Goods(){
		return $this->hasOne('App\Goods', 'goodsno', 'goodsno');
	}
	public function GoodsOption(){
		return $this->hasOne('App\GoodsOption', 'goodsno', 'goodsno');
	}
    //
}
