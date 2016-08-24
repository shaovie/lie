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
</head>
<body class="no-skin">
    <h3 class="header smaller lighter blue"><span style="margin-right:20px">sku属性列表</span><a href="/admin/SkuAttr/addPage" class="btn btn-primary">新建</a><span class="refresh">刷新</span></h3>
	<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center" style="width:80px;">sku属性名称</th>
					<th class="text-center" style="width:50px;">状态</th>
					<th class="text-center" style="width:50px;">时间</th>
					<th class="text-center" style="width:50px;">操作人</th>
					<th class="text-center" style="width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
            <?php foreach ($skuAttrList as $item):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['attr']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['state']?></td>
				<td style="text-align:left;vertical-align:middle;">
				<div>创建时间：<?php echo date('Y-m-d H:i:s', $item['ctime'])?></div>
				<div>修改时间：<?php echo date('Y-m-d H:i:s', $item['mtime'])?></div>
                </td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['m_user']?></td>
				<td style="text-align:center;vertical-align:middle;">
					<a class="btn btn-xs btn-info" href="/admin/SkuValue/listPage?attrId=<?php echo $item['id'];?>"><i class="icon-plus-sign-alt"></i> SKU值</a>&nbsp;&nbsp;
					<a class="btn btn-xs btn-info" href="/admin/SkuAttr/info?attrId=<?php echo $item['id'];?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
				</td>
			</tr>
            <?php endforeach?>
			</tbody>
		</table>
	</form>
</body>
</html>
