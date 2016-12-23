<!DOCTYPE html>
<html>
<head>
	<title>xray:<?php echo $html->title;?></title>
	<meta name="robots" content="index, all">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <link href="/css/xray.css" rel="stylesheet">
    <script src="/js/xray.js"></script>
</head>
<body>
<div id="wrapper" class="container">
	<div id="header" class="row">
		<h1><?php echo $html->title;?></h1>
        <a  class="btn btn-primary pull-right" target="__blank" href="//<?php echo $_SERVER["SERVER_NAME"]?>">go to site</a>
        <!--<ul class="panels"><li class="panel-item">Settings</li></ul>-->
	</div>
	<div id="content" class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label for="basic-url">Donor URL:</label>
            <div class="input-group">
              <span class="input-group-addon" id="area-field-url"><i class="fa fa-globe"></i></span>
              <input type="text" class="form-control" id="basic-url" name="url" aria-describedby="area-field-url" value="<?php echo $html->DataUrl;?>">
            </div>
            <hr/>
            <label for="basic-greenline">Show GreenLine:</label>

            <input type="checkbox" id="basic-greenline" name="greenline[show]" aria-describedby="area-field-showgreenline" <?php echo ($html->DataGreenlineShow=="true")?"checked":"";?>>
            <?php ?>
        </div>
	</div>
    <div id="buttons" class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <br/>
            <a id="submit" class="btn btn-success pull-right" href="javascript:xray.form.submit({form:$('#content'),url:'/admin.php',button:$('#submit')});">set</a>
        </div>
    </div>
</div>
</body>
</html>
