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
	
	/* Function Summary
	 *
	 * 1. @getGoodsList : 카테고리 별 상품 목록 가져오는 함수
	 *	- $categoryCode : 카테고리 코드 받는 변수
	 *	- $pageNo : 페이지 넘버링
	 *	- $pageOff : 한 페이지에 표시되는 상품 갯수
	 *
	 *
	 */
	
    public function getGoodsList($categoryCode, $pageNo, $pageOffset){
	
	$checkFunc = 'CategoryCodeCheck'; // 카테고리 코드가 올바른지 판별하기 위해 정의한 함수
	$checker = call_user_func_array(array($this,$checkFunc),array($categoryCode));
	
	if(!$checker){
		$error_return = [
			"error" => [
				"code" => "3005",
				"message" => "존재하지 않는 카테고리입니다.",
				"system_message" => "categoryCode가 존재하지 않거나 범위를 벗어났습니다"]
		];
				
		return response()->json($error_return,200,[],JSON_UNESCAPED_SLASHES);
	}
	
	
	if($pageNo < 0 || $pageOffset < 0){
		$error["code"] = "3006";
		$error["message"] = "<parameter>가 범위를 벗어났습니다.";
		$error["system_message"] = "";
	
		$error_display = [
			"error" => $error
		];
				
		
		return response()->json($error_display, 400, [], JSON_UNESCAPED_SLASHES);
	}
	
			
	if($pageNo == 0){
		$pageNo = 1;
	}
	if($pageOffset <= 0){
		$pageOffset = 10;
	}
	
	$Goods = new Goods();
	$GoodsOption = new GoodsOption();
	$goods_display = GoodsLink::where("category",$categoryCode)
		->leftjoin($Goods->getTable(), "gd_goods.goodsno", "=", "gd_goods_link.goodsno")
		->distinct()
		->leftjoin($GoodsOption->getTable(), "gd_goods_link.goodsno", "=", "gd_goods_option.goodsno")
		->where("gd_goods_option.go_is_display", '=', "1")
		->where("gd_goods_option.go_is_deleted", '=', "0")
		->skip(10 * ($pageNo - 1))
		->take($pageOffset)
		->get(["gd_goods_link.goodsno as goodsId", "gd_goods.goodsnm as goodsName", "gd_goods_option.price as goodsPrice", "gd_goods.img_i as thumbnailUrl"]);
	
	$return = [
		"data" => [
			"goodsLink" => $goods_display
		],
		"paging" => [
			"nextPageNo" => $pageNo+1
		]
	];
		
	return response()->json($return, 200, [], JSON_UNESCAPED_SLASHES);

    }
    public function getGoodsInfo($goodsId){
	$GoodsOption = new GoodsOption();
	
	$GoodsInformation = Goods::where('gd_goods.goodsno', '=', $goodsId)
			->leftjoin($GoodsOption->getTable().' as GO', 'gd_goods.goodsno', '=', 'GO.goodsno') 
			->where('go_is_display','=','1')
			->where('go_is_deleted','=','0')
			->get(['gd_goods.goodsnm as goodsName', 'GO.price as goodsPrice','totstock as goodsStock','gd_goods.img_i as thumbnailUrl','shortdesc as goodsIntroduction','longdesc as goodInfo']);
	
	$return = [
		"data" => $GoodsInformation
	];
	
	return response()->json($return, 200,[], JSON_UNESCAPED_SLASHES);
	
	
    }
    public function CategoryCodeCheck($categoryCode){
	$Checker = GoodsLink::where("category","=",$categoryCode)
		->count();
	return $Checker;

    }
		


}
