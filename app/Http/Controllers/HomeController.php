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
	$Categories = Categories::where("hidden","=","0")
                                        ->where("level_auth","=","0")
                                        ->where(DB::raw("LENGTH(category)"),"<=","3")
                                        ->orderby("sort")
                                        ->orderby("category")
                                        ->limit(10)
                                        ->get(["category","catnm"]);
	

	$i = 0;
        foreach ($Categories as $GoodsCategorycolumn) {
           $GoodsCategoryList[$i]["no"] = $GoodsCategorycolumn::pluck("category");
           $GoodsCategoryList[$i]["name"] = $GoodsCategorycolumn::pluck("catnm");
           $GoodsSubCategory = Categories::where("hidden", "=", "0")
               ->where("level_auth", "=", "0")
               ->where(DB::raw("LENGTH(category)"), "=", "6")
               ->where("category", "like", "$GoodsCategorycolumn[category]%")
               ->orderby("category")
		->distinct()
               ->get(["category","catnm"]);
	    $i++;
	}
	$GoodsCategoryList = Categories::where(DB::raw("length(category)"), "<=","3")
			->orderby("sort")
			->get(["category", "catnm"]);
	
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
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
