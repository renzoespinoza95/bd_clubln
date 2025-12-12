<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>QjSearch</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.1.3/materia/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<style>
		body { background-color: #fafafa; }
		.container { margin: 150px auto; max-width: 960px; }
		.w100 {display:block;width:100%;}
		.dn {display:none;}
		.eres {color:#f00;padding:5px;}
		.sres {color:#168c00;padding:5px;}
		.city_block {padding:5px;border:1px solid #ccc;margin:5px;display:inline-block;}
	</style>
</head>
<body>
<div class="container">
  <h1>jQuery QjSearch: DOM Filter Demos</h1>
<h2>Search on block's</h2>
<div class="w100"><input type="text" id="search_field" placeholder="city" data-qjs="#cities .city_block" data-qjs-0="#emptyresult" data-qjs-1="#resultcountry" data-qjs-res="#qjlenres" autofocus="" class="form-control"></div>
<div class="w100" id="cities">
<div id="emptyresult" class="eres dn">Results not found.</div>
<div id="resultcountry" class="sres">Results: <span id="qjlenres"></span></div>
<div class="city_block label" data-cnt="USA">New York</div><div class="city_block" data-cnt="Great Britain">London</div><div class="city_block" data-cnt="Russian">Moscow</div><div class="city_block" data-cnt="Thailand">Bangkok</div><div class="city_block" data-cnt="Japan">Tokyo</div><div class="city_block" data-cnt="Ukraine">Kiev</div></div>


<hr>

<h2>Search on table</h2>
<div class="w100"><input type="text" id="search_field2" placeholder="city" data-qjs=".table tbody tr" autofocus="" class="form-control"></div>
<table class="table table-bordered table-striped">
<thead class="thead-dark">
<tr><td>ID</td><td>Name</td><td>Country</td></tr>
</thead>
<tbody>
<tr><td>1</td><td>New York</td><td>USA</td></tr>
<tr><td>2</td><td>London</td><td>Great Britain</td></tr>
<tr><td>3</td><td>Moscow</td><td>Russia</td></tr>
<tr><td>4</td><td>Bagkok</td><td>Thailand</td></tr>
<tr><td>5</td><td>Tokyo</td><td>Japan</td></tr>
<tr><td>6</td><td>Kiev</td><td>Ukraine</td></tr>
</tbody>
</table>
</div>
		<script src="qjsearch.js"></script>
		<script>
			$("#search_field,#search_field2").qjsearch();
		</script>

	</body>
	</html>
