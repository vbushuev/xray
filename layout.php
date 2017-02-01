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
	<div id="content">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            <label for="basic-url">Donor URL:</label>
	            <div class="input-group">
	              <span class="input-group-addon" id="area-field-url"><i class="fa fa-globe"></i></span>
	              <input type="text" class="form-control" id="basic-url" name="url" aria-describedby="area-field-url" value="<?php echo $html->DataUrl;?>">
	            </div>
	        </div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<label for="basic-greenline">Show GreenLine:</label>
				<input type="checkbox" id="basic-greenline" name="greenline[show]" aria-describedby="area-field-showgreenline" <?php echo ($html->DataGreenlineShow=="true")?"checked":"";?>>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<label for="basic-translate">Use Translator:</label>
				<input type="checkbox" id="basic-translate" name="translate[use]" aria-describedby="area-field-usetranslate" <?php echo ($html->DataTranslateUse=="true")?"checked":"";?>>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<label for="basic-language">Language:</label>
				<select id="basic-language" name="translate[lang]" aria-describedby="area-field-language">
					<<option value="fr" <?php echo ($html->DataTranslateLang=="fr")?"selected":"";?>>French</option>
					<<option value="en" <?php echo ($html->DataTranslateLang=="en")?"selected":"";?>>English</option>
				</select>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<a href="https://dictionary.gauzymall.com?lang=fr">Dictionary editor</a>
			</div>
		</div>
		<div class="row">
			<h3>Hacks</h3>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="title" for="additional-cookies">Preset cookies
					<a class="button add" data-ref="#cookie" href="javascript:{0}"><i class="fa fa-plus"></i></a>
				</div>
				<?php echo $html->DataCookie;?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="title" for="basic-greenline">Subtitutions:
					<a class="button add" data-ref="#substitutions" href="javascript:{0}"><i class="fa fa-plus"></i></a>
				</div>
				<?php  echo $html->DataHacksSubstitutions;?>
			</div>
		</div>
	</div>
    <!--<div id="buttons" class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <br/>
            <a id="submit" class="btn btn-success pull-right" href="javascript:xray.form.submit({form:$('#content'),url:'/admin.php',button:$('#submit')});">set</a>
        </div>
    </div>-->
</div>
<script>
(function($){
	function initResponsibility(){
		$(".delete").unbind("click").on("click",function(e){
			var todel = $(this).parent($(this).attr("data-ref"));
			todel.fadeOut(400,function(){todel.remove()});
			xray.form.submit({form:$('#content'),url:'/admin.php',button:$('#submit'),callback:function(d){console.debug(d);}});
		});
		$(".add").unbind("click").on("click",function(e){
			var name = $(this).attr("data-ref").replace(/#/,''),toadd = $('#'+name),
				i = $(this).parent().next(".listvalues:first").children("li").length,
				uldata = $(this).parent().next(".listvalues:first").attr("data");
				html = '<li class="listvalue"><i class="fa fa-square-"></i>&nbsp;';
			console.debug("Li count = "+i);
			html+= '<div class="editable inline key" data-rel="#'+name+'-'+i+'" data-field="name"></div>&nbsp;';
			html+= '<i class="fa fa-exchange"></i>&nbsp;';
			html+= '<div class="editable inline value" data-rel="#'+name+'-'+i+'" data-field="value"></div>&nbsp;';
			html+= '<a class="button delete" data-ref=".listvalue" href="javascript:{0}"><i class="fa fa-times"></i></a>';
			html+= '<input id="'+name+'-'+i+'" type="hidden" name="'+uldata+'" value=""></li>';
			var app = toadd.append(html);
			initResponsibility();
			app.children(".key").click();
		});
		$(".editable").unbind("click").on("click",function(e){
			if(!$(this).hasClass("editting")){
				$(this).attr("contentEditable","true")
					.addClass("editting");
					//.append('<a style="z-index:1000" class="submit" href="javascript:{xray.form.submit({form:$(\'#content\'),url:\'/admin.php\',button:$(this),callback:function(d){console.debug(d);}});}"><i class="fa fa-check">&nbsp;</i></a>');
				$(this).focus();
			}
			else{
				var edt = $(this);
				xray.form.submit({form:$('#content'),url:'/admin.php',button:$('#submit'),callback:function(d){
					edt.removeAttr("contentEditable").removeClass("editting").find('.submit').remove();
				}});
			}
		}).unbind("blur").on("blur",function(){
			if($(this).hasClass("editting")){
				$(this).removeAttr("contentEditable")
					.removeClass("editting")
					.find('.submit').remove();
			}
		}).unbind("keydown").on("keydown",function(e){
			var keycode = (e.keyCode ? e.keyCode : e.which);
        	if(keycode == '13'){
        		e.preventDefault();
				e.stopPropagation();
				$(this).click();
        	}
		}).unbind("keyup").on("keyup",function(e){
			var inp = $($(this).attr("data-rel")),field = $(this).attr("data-field");
			if(field=="value") inp.val($(this).text().trim());
			else if(field=="name"){
				//var fn = $(this).parent(".listvalues").attr("data");
				var fn = inp.attr("name").replace(/\[.+?\]$/,'');
				inp.attr("name",fn+"["+$(this).text().trim()+"]");
			}
		});
	}
    $(document).ready(function(){
        $(document).keypress(function(event){
        	var keycode = (event.keyCode ? event.keyCode : event.which);
        	if(keycode == '13'){
        		console.log("Enter pressed. Saving settings")
                xray.form.submit({form:$('#content'),url:'/admin.php',button:$('#submit'),callback:function(d){console.debug("settings saved.");}});
        	}
        });
		$("input[type=checkbox],select").on("change",function(e){
			console.log("Checkbox. Saving settings")
			xray.form.submit({form:$('#content'),url:'/admin.php',button:$('#submit'),callback:function(d){console.debug("settings saved.");}});
		})
		initResponsibility();
	});
})(jQuery);
</script>
</body>
</html>
