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
	
	//订单列表
	$("#order-status").change(function(){
		var status =$("#order-status").val();
		$.get('/vip/order/list',{
			status : status,
			},
		function(data){
		     $('.vip-right').html(data);	
		});
		
		
	});
	//订单列表 操作
	$(".order-opera").click(function(){
		var types = $(this).attr('id');
		var  orderId= $(this).parent("td").attr('id');
		if (types == 1){
			location.href="/vip/order/pay?id="+orderId;
		}
		if (types == 3){
			$(".order-rate-div").show();
			$(".rate-content").attr('id',orderId);
			return;
		}
		$.get('/vip/order/alter',{
			order_id : orderId,
			type:types,  
			},
		function(data){
		    if (data.status=='success'){
		    	ds.dialog({
					   title : '消息提示',
					   content : '操作成功',
					   timeout:1
					});	
		    	var status =$("#order-status").val();
		    	$.get('/vip/order/list',{
	    			status : status,
	    			},
	    		function(data){
	    		     $('.vip-right').html(data);	
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
	
	//订单列表 提交评价
	$(".commit-rate").click(function(){
		var content = $(".rate-content").val();
		var  orderId= $(".rate-content").attr('id');
		$.get('/item/rate/add',{
			order_id : orderId,
			content:content,  
			},
		function(data){
		    if (data.status=='success'){
		    	ds.dialog({
					   title : '消息提示',
					   content : '操作成功',
					   timeout:1
					});	
		    	var status =$("#order-status").val();
		    	$.get('/vip/order/list',{
	    			status : status,
	    			},
	    		function(data){
	    		     $('.vip-right').html(data);	
	    		});
		    }else if (data.status == 'error'){
		    	ds.dialog({
					   title : '消息提示',
					   content : data.desc,
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
});