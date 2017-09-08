<?php

namespace App\Http\Controllers;
use App\Goods;
use App\PopularGoods;
use App\GoodsOption;
use App\Categories;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * 1. getMain : 메인 페이지에 표시 될 인기 있는 상품 리스트 출력
     * 2. getCategoriesList : 카테고리 버튼을 눌렀을 때 카테고리 목록을 출력
     */
    public function getMain() 
    {
	/**
	 *  variable 
	 *  - $Goods : gd_goods 테이블의 정보를 담고 있는 변수
	 *  - $GoodsOption : gd_goods_option 테이블의 정보를 담고 있는 변수
	 *  - $goods_display : gd_goods_display 테이블의 정보를 담고 있는 변수, 인기 있는 상품이 어떤 것인지 정보를 담고 있으며, 해당 정보는 kurly.com의 admin 계정을 통해 변경할 수 있다.
	 *  
	 *  column  
	 *  @goodsId : 상품코드
	 *  @goodsName : 상품이름
	 *  @goodsPrice : 상품가격
	 *  @thumbnailUrl : 썸네일이미지
	 */
	$Goods = new Goods();
	$GoodsOption = new GoodsOption();
		
	
	$goods_display = PopularGoods::leftjoin($Goods->getTable(), "gd_goods.goodsno", "=", "gd_goods_display.goodsno")
		->where("gd_goods.goodsno","!=","6655")
		->where("mode","0") // mode : gd_goods_display 테이블에서 가져옴. mode에 따라 원하는 상품리스트를 선택 가능함. 0번 : 인기 상품 목록
		->orderby("sort") // sort : gd_goods_display. 상품 목록의 순서 배치하는 용도로 사용.
		->leftjoin($GoodsOption->getTable(), "gd_goods_display.goodsno", "=", "gd_goods_option.goodsno") // gd_goods_option 테이블 사용하기 위해 join.
		->where("gd_goods_option.go_is_display","=","1") // 일반적으로 gd_goods_option테이블에는 상품의 옵션 별 설정이 있음. go_is_display가 '1'인 경우의 레코드는 일반적으로 유저에게 보이는 옵션의 정보를 담고 있는 레코드이기에 조건 추가
		->where("gd_goods_option.go_is_deleted","=","0") // go_is_deleted 컬럼이 '1'인 경우 삭제된 상품 정보임.
		->distinct()
		->get(['gd_goods_display.goodsno as goodsId', 'gd_goods.goodsnm as goodsName','gd_goods_option.price as goodsPrice','gd_goods.img_i as thumbnailUrl']);
	
	$popular_product = $goods_display;
	$return = [
		"data" => [
			"popularGoods" => $popular_product 
		],
	];

	return response()->json($return, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function getCategoriesList()
    {
	
	/**
	 *  variable : 
	 *  - $GoodsCategory : 최상위 카테고리들을 객체로 담고 있는 변수 
	 *  - $GoodsCategoryList : 전체 카테고리에 대한 정보를 담고 있는 객체 변수 
	 *  - $upperIter : 최상위 카테고리를 순회할 때 사용
	 *  - $subIter : 하위 카테고리를 순회할 때 사용
	 *
	 *  column  
	 *  @categoryCode : 카테고리 코드
	 *  @categoryName : 카테고리 이름
	 */
	
	$GoodsCategory = Categories::where(DB::raw("LENGTH(gd_category.category)"), "=","3")// 최상위 카테고리의 경우 000 처럼 3자리로 표현됨.
			->where('hidden','0')// 사용하지 않는 카테고리가 존재하기에 hidden 옵션을 통해 표현
			->orderby('sort')// sort 컬럼을 기준으로 정렬하여 카테고리 출력해줌.
			->take(10)// 실제 사용하는 카테고리가 10개
			->get(['category','catnm']);
	$upperIter = 0;
	foreach($GoodsCategory as $GoodsCategoryIterator){
		$GoodsCategoryList[$upperIter]['categoryCode'] = $GoodsCategoryIterator['category']; // 카테고리 코드를 객체에 담음
		$GoodsCategoryList[$upperIter]['categoryName'] = $GoodsCategoryIterator['catnm']; // 카테고리 이름을 객체에 담음
		$SubCategory = Categories::where('hidden', '0')
				->where(DB::raw('LENGTH(category)'), '=','6') // 일반적으로 하위 카테고리는 000000 처럼 6자리수로 제작되어있음.
				->where('category', 'LIKE', "$GoodsCategoryIterator[category]%") // 최상위 카테고리 3자리는 각각 해당 카테고리의 하위 카테고리들의 코드 앞 3자리를 차지함.
				->orderby('category')
				->get(["category","catnm"]);

		$subIter = 0;
		foreach($SubCategory as $subc){
			// 상위 카테고리의 배열에 해당하는 하위카테고리들의 정보 등록
			$GoodsCategoryList[$upperIter]['subcategories'][$subIter]['categoryCode'] = $subc['category']; 
			$GoodsCategoryList[$upperIter]['subcategories'][$subIter]['categoryName'] = $subc['catnm'];
			$subIter++;
		}
		$upperIter++;
	}
			
		
	$return = [
		"data" => [
			"categories" => $GoodsCategoryList
		],
	];
	return response()->json($return, 200, [], JSON_UNESCAPED_SLASHES);
        //
    }

}
