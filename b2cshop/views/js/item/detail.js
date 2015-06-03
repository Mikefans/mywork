$(document).ready(function(){
	$(".color--ul li").click(function(){
		var color = $(this).attr('id');
		$("#item-color-set").val(color);
		$(".color--ul li").css("background-color",'#DDDDDD');
		$(this).css("background-color",'#FFFF33');
	});
	$(".marque--ul li").click(function(){
		var marque = $(this).attr('id');
		$("#item-marque-set").val(marque);
		$(".marque--ul li").css("background-color",'#DDDDDD');
		$(this).css("background-color",'#FFFF33');
	});

	$(".good-description").click(function(){
		$('.good-rate').css("background-color",'#DDDDDD');
		$(this).css("background-color",'white');
		$('.description-content').show();
		$('.rate-content').hide();
		
	});
	$(".good-rate").click(function(){
		$('.good-description').css("background-color",'#DDDDDD');
		$(this).css("background-color",'white');
		$('.rate-content').show();
		$('.description-content').hide();
		
	});
	$(".next-img-ul li").click(function(){
		var srcs = $(this).attr('id');
		$('.main-img').attr("src",srcs);  
		
	});
	
	$(".add-to-car").click(function(){
		var colors = $("#item-color-set").val();
	    var marques = $("#item-marque-set").val();
	    var num = $("#buy-item-num").val(); 
		 $.post("/vip/car/addTocar",
				  {
				    item_color:colors,
				    item_marque:marques,
				    item_id:itemId,
				    item_num:num,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '加入成功',
							   timeout:1,	  
							});
						  
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
	
	$(".to-buy-now").click(function(){
		var colors = $("#item-color-set").val();
	    var marques = $("#item-marque-set").val();
	    var num = $("#buy-item-num").val(); 
		 $.post("/vip/order/add",
				  {
				    item_color:colors,
				    item_marque:marques,
				    item_id:itemId,
				    item_num:num,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  location.href= '/vip/order/order';
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