<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
</head>
<body>
<div id="goods"></div>
<script>
	var settings = {
		"async" : true,
		"crossDomain" : true,
		"url" : "http://dev-jh-api.kurly.com/v1/main",
		"method" : "GET",
		"headers" : {}
	}
	$.ajax(settings).done(function(response){
		console.log(response);
		$("#goods").html(response.data.popularGoods[1].goodsName);
	});
	

</script>
</body>
</html>
