<div id="divLoading" style="color:grey;display:none;text-align:center;">
	<img id="imgLoading" src="img/loading.gif" style="margin:5px;"/>
	<div style="font-size:11px;">We are processing your request ...</div>
</div>
<script>
	$("#divLoading").dialog({
		width : 'auto',
		height : 'auto',
		autoOpen : false,
		modal : true,
		position: { my: 'top', at: 'top+260' },
		open : function(event, ui) {
			$(this).siblings('.ui-dialog-titlebar').remove();
		}
	});
</script>