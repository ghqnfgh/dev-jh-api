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
     * @return \Illuminate\Http\Response
     */
    public function getMain() 
    {
	$Goods = new Goods();
	$GoodsOption = new GoodsOption();
	$goods_display = PopularGoods::leftjoin($Goods->getTable(), "gd_goods.goodsno", "=", "gd_goods_display.goodsno")
		->where("mode","0")
		->orderby("sort")
		->leftjoin($GoodsOption->getTable(), "gd_goods_display.goodsno", "=", "gd_goods_option.goodsno")
		->where("gd_goods_option.go_is_display","=","1")
		->where("gd_goods_option.go_is_deleted","=","0")
		->distinct()
		->get(['gd_goods_display.goodsno as goodsId', 'gd_goods.goodsnm as goodsName','gd_goods.img_i as thumbnailUrl','gd_goods_option.price as goodsPrice']);
	$popular_product = $goods_display;
	$return = [
		"data" => [
			"popularGoods" => $popular_product 
		],
	];

	return response()->json($return, 200, [], JSON_UNESCAPED_SLASHES);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategoriesList()
    {
	$SubCategories = new Categories();
	$GoodsCategory = Categories::where("gd_category.hidden","=","0")
                                        ->where("gd_category.level_auth","=","0")
                                        ->where(DB::raw("LENGTH(gd_category.category)"),"<=","3")
                                        ->orderby("gd_category.sort")
                                        ->orderby("gd_category.category")
                                        ->limit(10)
                                        ->get(["gd_category.category","gd_category.catnm"]);
	if($GoodsCategory) {
            $i = 0;
            foreach ($GoodsCategory as $GoodsCategorycolumn) {
                $GoodsCategoryList[$i]["categoryCode"] = $GoodsCategorycolumn["category"];
                $GoodsCategoryList[$i]["categoryName"] = $GoodsCategorycolumn["catnm"];
                $GoodsSubCategory = Categories::where("hidden", "=", "0")
                    ->where("level_auth", "=", "0")
                    ->where(DB::raw("LENGTH(category)"), "=", "6")
                    ->where("category", "like", "$GoodsCategorycolumn[category]%")
                    ->orderby("category")
                    ->get(["category","catnm"]);
                //print_r($GoodsSubCategory);
                $j = 0;
                foreach ($GoodsSubCategory as $GoodsSubCategorycolumn) {
                    if ($GoodsCategorycolumn["category"] == "126") {
                        $hashtag = "#";
                    } else {
                        $hashtag = "";
                    }
                    $GoodsCategoryList[$i]["subcategories"][$j]["categoryCode"] = $GoodsSubCategorycolumn["category"];
                    $GoodsCategoryList[$i]["subcategories"][$j]["categoryName"] = $hashtag . $GoodsSubCategorycolumn["catnm"];
                    $j++;
                }
                $i++;
            }
        }
	

	$return = [
		"data" => [
			"category" => $GoodsCategoryList
		],
	];
	return response()->json($return, 200, [], JSON_UNESCAPED_SLASHES);
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     
    public function store(Request $request)
    {
        //
    }

    *
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    public function show($id)
    {
        //
    }

    **
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    public function edit($id)
    {
        //
    }

    **
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    public function update(Request $request, $id)
    {
        //
    }

    **
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    public function destroy($id)
    {
        //
    }
	*/
}
