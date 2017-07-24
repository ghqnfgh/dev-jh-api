<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Goods;
use App\GoodsOption;
use App\Categories;
use App\GoodsLink;

use DB;

class GoodsController extends Controller
{
    public function getGoodsList($categoryCode){
	$Goods = new Goods();
	$GoodsOption = new GoodsOption();
	$goods_display = GoodsLink::where("category",$categoryCode)
		->leftjoin($Goods->getTable(), "gd_goods.goodsno", "=", "gd_goods_link.goodsno")
		->distinct()
		->leftjoin($GoodsOption->getTable(), "gd_goods_link.goodsno", "=", "gd_goods_option.goodsno")
		->where("gd_goods_option.go_is_display", '=', "1")
		->where("gd_goods_option.go_is_deleted", '=', "0")
		->skip(10)
		->take(10)
		->get(["gd_goods_link.goodsno as goodsId", "gd_goods.goodsnm as goodsName", "gd_goods_option.price as goodsPrice", "gd_goods.img_i as thumbnailUrl"]);
	
	$return = [
		"data" => [
			"goodsLink" => $goods_display
		],
	];
		
	return response()->json($return, 200, [], JSON_UNESCAPED_SLASHES);

    }
		


}
