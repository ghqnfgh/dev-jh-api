<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Goods;
use App\GoodsOption;
use App\Categories;
use App\GoodsLink;
use App\Http\Middleware\Cors;

use DB;

class GoodsController extends Controller
{
	
	/* Function Summary
	 *
	 * 1. getGoodsList : 카테고리 별 상품 목록 가져오는 함수
	 *	- $categoryCode : 카테고리 코드 받는 변수
	 *	- $pageNo : 페이지 넘버링
	 *	- $pageOff : 한 페이지에 표시되는 상품 갯수
	 * 2. getGoodsInfo : 상품에 대한 간략한 정보를 가져올 함수
	 *	- $goodsId : 정보를 보기 원하는 상품의 id
	 * 3. CategoryCodeCheck : 올바른 카테고리 코드인지 확인할 함수 
	 * 4. GoodsIdCheck : 올바른 상품 id인지 확인하는 함수 
	 *
	 */
	
    public function getGoodsList($categoryCode, $pageNo, $pageOffset){
	
	/*  variable
	 *  - $categoryCode : 상품 리스트 출력하기 위한 카테고리 코드
	 *  - $pageNo : 페이징 위해 사용하는 변수
	 *  - $pageOffset : 한 페이지에 몇 개의 상품 출려할건지 결정
	 *  
	 *  column  
	 *  @goodsId : 상품코드
	 *  @goodsName : 상품이름
	 *  @goodsPrice : 상품가격
	 *  @thumbnailUrl : 썸네일이미지
	 */
	$checkFunc = 'CategoryCodeCheck'; // 카테고리 코드가 올바른지 판별하기 위해 정의한 함수
	$checker = call_user_func_array(array($this,$checkFunc),array($categoryCode));
	
	if(!$checker){
		$error_display = [
			"error" => [
				"code" => "3005",
				"message" => "존재하지 않는 카테고리입니다.",
				"system_message" => "categoryCode가 존재하지 않거나 범위를 벗어났습니다"]
		];
				
		return response()->json($error_display,400,[],JSON_UNESCAPED_SLASHES);
	}
	
	// 페이징 익셉션 처리
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
	if($pageOffset == 0){
		$pageOffset = 10;
	}
	
	$Goods = new Goods(); // gd_goods테이블 연결
	$GoodsOption = new GoodsOption(); // gd_goods_option 테이블 연결

	$GoodsCategory = Categories::where("gd_category.category", "=", $categoryCode)
		->get(['catnm as categoryName']);
	
	$goods_display = GoodsLink::where("category",$categoryCode) // 찾길 원하는 카테고리 코드 적용
		->leftjoin($Goods->getTable(), "gd_goods.goodsno", "=", "gd_goods_link.goodsno") // 상품의 간단한 정보를 가져오기 위해 gd_goods 테이블 연결
		->distinct()
		->leftjoin($GoodsOption->getTable(), "gd_goods_link.goodsno", "=", "gd_goods_option.goodsno") // 상품 가격정보 확인 위해 gd_goods_option 테이블 연결
		->where("gd_goods_option.go_is_display", '=', "1") 
		->where("gd_goods_option.go_is_deleted", '=', "0")
		->where("gd_goods.img_y", "!=", "")
		->where("gd_goods_option.price", "!=", "0")
		->where("gd_goods.longdesc","!=","")
		->where("gd_goods.totstock",">","0")
		->orderby("price","asc")
		->skip(10 * ($pageNo - 1)) // 입력받은 pageNo 변수를 활용해 페이징
		->take($pageOffset) // 입력받은 pageOffset 변수 활용해 한 페이지에 상품 몇 개를 표시할 것인지 구성
		->get(["gd_goods_link.goodsno as goodsId", "gd_goods.goodsnm as goodsName", "gd_goods_option.price as goodsPrice", "gd_goods.img_y as thumbnailUrl"]);
	
	$return = [
		"category" => $GoodsCategory,
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

	/*  variable
	 *  - $goodsId : 요청받은 상품 id 
	 *  
	 *  column  
	 *  @goodsName : 상품이름
	 *  @goodsPrice : 상품가격
	 *  @goodsStock : 상품재고량
	 *  @thumbnailUrl : 썸네일이미지
	 *  @goodsIntroduction : 상품에 대한 간략한 소개
	 *  @goodsInfo : 상품에 대한 자세한 소개 이미지
	 *
	 */

	$checkFunc = 'GoodsIdCheck'; // 상품 Id가 올바른지 판별하기 위해 정의한 함수
	$checker = call_user_func_array(array($this,$checkFunc),array($goodsId));
	
	if(!$checker){
		$error_return = [
			"error" => [
				"code" => "3004",
				"message" => "존재하지 않는 상품입니다.",
				"system_message" => "goodsId가 존재하지 않습니다. goodsId 범위를 벗어났거나 존재하지 않는 goodsId입니다."]
		];
				
		return response()->json($error_return,400,[],JSON_UNESCAPED_SLASHES);
	}

	$GoodsOption = new GoodsOption();
	
	$GoodsInformation = Goods::where('gd_goods.goodsno', '=', $goodsId)
			->leftjoin($GoodsOption->getTable().' as GO', 'gd_goods.goodsno', '=', 'GO.goodsno') 
			->where('go_is_display','=','1')
			->where('go_is_deleted','=','0')
			->get(['gd_goods.goodsnm as goodsName', 'GO.price as goodsPrice','totstock as goodsStock','gd_goods.img_y as thumbnailUrl','shortdesc as goodsIntroduction','longdesc as goodInfo']);
	
	$return = [
		"data" => $GoodsInformation
	];
	
	return response()->json($return, 200,[], JSON_UNESCAPED_SLASHES);
	
	
    }

    public function CategoryCodeCheck($categoryCode){
	$Checker = Categories::where("category","=",$categoryCode)
		->count();
	return $Checker;

    }

    public function GoodsIdCheck($goodsId){
	$Checker = Goods::where("goodsno","=",$goodsId)
		->count();
	return $Checker;

    }
	
		


}
