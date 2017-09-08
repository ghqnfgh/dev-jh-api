<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
</head>
<body>
<table id="goods"></table>
<script>
	var settings = {
		"async" : true,
		"crossDomain" : true,
		"url" : "http://dev-jh-api.kurly.com/v1/main",
		"method" : "GET",
		"headers" : {}
	}
	$.ajax(settings).done(function(response){
		var table ="<tr><th>goodsId</th><th>goodsName</th><th>goodsPrice</th><th>thumbnailUrl</th></tr>";
		console.log(response);
		for(i = 0; i < response.data.popularGoods.length; i++){
			table += "<tr><td>" + response.data.popularGoods[i].goodsId +
				"</td><td>" + response.data.popularGoods[i].goodsName +
				"</td><td>" + response.data.popularGoods[i].goodsPrice +
				"</td><td>" + response.data.popularGoods[i].thumbnailUrl +
				"</td></tr>";
		}
		$("#goods").html(table);

	});
	

</script>
</body>
</html>
