$(document).ready(function(){
//下拉列表级联选择事件
	$(".sql-commit").click(function(){
		var select = $(".select-text").val();
		var  where = $(".where-text").val();
		var module = $(".module").val();
		var tables = $(".tables").val();
		$.get('/db/db/execute',{
			where:where,
			select:select,
			table:tables,
			module:module,
			},
		function(data){
		   $('.sql-result').html(data);
		});
	});	

});