$(document).ready(function(){
//下拉列表级联选择事件
	$(".order-opera").click(function(){
		var types = $(this).attr('id');
		var  orderId= $(this).parent("td").attr('id');
		var orderAddNum = $(".order-add-num"+orderId).val();
		var orderAdd = $(".order-add"+orderId).val();
		$.get('/vip/order/alter',{
			order_id : orderId,
			type:types,
			order_add:orderAdd,
			order_add_num:orderAddNum,
			},
		function(data){
		    if (data.status=='success'){
		    	ds.dialog({
					   title : '消息提示',
					   content : '操作成功',
					   timeout:1
					});	
		    }else{
		    	ds.dialog({
					   title : '消息提示',
					   content : '操作失败',
					   timeout:1
					});	
		    }
		});
	});	
	$("#order-status").change(function(){
		var status =$("#order-status").val();
		$.get('/vip/order/list',{
			status : status,
			},
		function(data){
		     $('.right-div').html(data);	
		});
		
		
	});
});