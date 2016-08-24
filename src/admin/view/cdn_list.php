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
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue"><span style="margin-right:20px">总数：<?php echo $totalNum;?></span><span class="refresh">刷新</span></h3>
	<form action="/admin/Cdn/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<span style="margin-right:10px;">域名</span>
                    <input style="margin-right:10px;margin-top:10px;width:160px; height:34px; line-height:28px; padding:2px 5px"
                    name="searchDomain" id="searchDomain" type="text"
                    value="<?php if (!empty($search['searchDomain'])) {echo $search['searchDomain'];} ?>" placeholder="模糊查询">
				</li>
				<li style="float:left;list-style-type:none;">
					<input type="submit" class="btn btn-sm btn-primary" style="margin-right:10px;margin-top:10px;" value="搜索"></li>
			</td>
		</tr>
	</tbody>
	</table>
	</form>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:180px;">CDN域名</th>
			<th class="text-center" style="width:280px;">CNAME</th>
			<th class="text-center" style="width:80px;">回源地址</th>
			<th class="text-center" style="width:120px;">状态</th>
			<th class="text-center" style="width:250px;">描述</th>
			<th class="text-center" style="width:180px;">时间</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($dataList as $one):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <?php echo $one['domain']?>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <?php echo $one['cname']?>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <?php echo $one['source']?>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <?php echo $one['state']?>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <?php echo $one['remark']?>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div>创建：<?php echo $one['ctime']?></div>
                <div>修改：<?php echo $one['mtime']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <a class="btn btn-xs btn-info" onclick="modifySource(
                <?php echo !empty($one['domain']) ? ("'" . $one['domain'] . "'"): "''" ?>,
                <?php echo !empty($one['source']) ? ("'" . $one['source'] . "'"): "''" ?>
                )" >修改
                </a>
                <a class="btn btn-xs btn-info" href="/admin/Cdn/del?id=<?php echo $one['id'];?>" onclick="return confirm(&#39;确认删除吗？&#39;);return false;">删除</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
	<!--弹窗-->
	<div id="modal-modifysource" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">修改回源地址</h4>
			</div>
			<div class="modal-body">
				<div class="form-group" style="height:25px;">
					<label class="col-sm-3 control-label no-padding-right">CDN域名：</label>
					<div class="col-sm-9">
						<input type="text" name="modalp-domain" id="modalp-domain" class="span5" value="" readonly="readonly">
					</div>
                </div>
				<div class="form-group" style="height:25px;">
					<label class="col-sm-3 control-label no-padding-right">回源地址：</label>
					<div class="col-sm-9">
						<input type="text" name="modalp-source" id="modalp-source" class="span5" value="">
					</div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="modalp-confirm" name="modalp-confirm" value="yes">提交</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
    <script>
    function modifySource(domain, source){
      $('#modalp-domain').val(domain);
      $('#modal-modifysource').modal('show');
    }
    $('#modalp-confirm').click(function(){
        var url = "/admin/Cdn/modifySource";
        $.post(url,
            {domain:$('#modalp-domain').val(),
             source:$('#modalp-source').val()},
             function(data){
                if(data.code==0) {
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            }, 'json');
    });
    </script>
</body>
</html>
