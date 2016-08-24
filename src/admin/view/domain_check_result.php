<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10">    
	<link href="/asset/css/bootstrap.min.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">
	<link href="/asset/css/ace.min.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">

    <link rel="stylesheet" href="/asset/css/ace-rtl.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <link rel="stylesheet" href="/asset/css/ace-skins.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/asset/css/ace-ie.min.css<?php echo '?v=' . ASSETS_VERSION;?>" />
    <![endif]-->
	<link href="/asset/css/common.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <script type="text/javascript" src="/asset/js/jquery-1.10.2.min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/common.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/bootstrap.min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script> 
    <link type="text/css" rel="stylesheet" href="/asset/css/default.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome-ie7.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <![endif]-->
	<script type="text/javascript" src="/asset/js/goods.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <script type="text/javascript" src="/asset/js/datetimepicker.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
    <?php foreach ($dataList as $one):?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">域名：</label>
			<div class="col-sm-9">
				<input type="text" maxlength="100" class="span7" readonly="readonly" value="<?php echo $one['domain']?>">
                <span class="help-inline"><?php echo $one['result']?></span>
                <?php if ($one['canOrder']):?>
				<button type="button" class="btn btn-primary span2" onclick="toOrder('<?php echo $one["domain"]?>')">购买</button>
                <?php endif?>
			</div>
		</div>
    <?php endforeach?>
    </form>
	<script>
    function toOrder(domain) {
        var url = "/admin/Domain/order?isAjax=1";
        $.post(url,
            {domain:domain},
             function(data){
                if(data.code==0) {
                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
                return true;
            }, 'json');
    }
	</script>
</body>
</html>
