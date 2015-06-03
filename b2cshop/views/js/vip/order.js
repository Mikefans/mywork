$(document).ready(function(){
	$(".commit-order").click(function(){
		var addId = $("input[name=chooseadd]:checked").val(); 
		if (addId == '' || typeof addId == 'undefined'){
			  ds.dialog({
				   title : '消息提示',
				   content : '请选择地址',
				   timeout:2	  
				});
			  return;
		}
		 $.post("/vip/order/create",
				  {
				    address_id:addId,
				    item_id:1,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  
						  location.href= '/vip/order/pay?id='+data.result.id;
					  }else if (data.status == 'error') {
						  ds.dialog({
							   title : '消息提示',
							   content : data.desc,
							   timeout:2	  
							});
					  }else {
						  ds.dialog({
							   title : '消息提示',
							   content : '请求错误，请刷新重试！',
							   timeout:2	  
							});
					  }
				  });
	});
	
	$(".pay-order").click(function(){
		var addId = $("input[name=chooseadd]:checked").val(); 
		 $.post("/vip/order/money",
				  {
				    order_id:orderId,
				    item_id:1,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '支付成功',
							   timeout:2	  
							});
						  location.href= '/vip/order/list';
					  }else if (data.status == 'error') {
						  ds.dialog({
							   title : '消息提示',
							   content : data.desc,
							   timeout:2	  
							});
					  }else {
						  ds.dialog({
							   title : '消息提示',
							   content : '请求错误，请刷新重试！',
							   timeout:2	  
							});
					  }
				  });
	});
});