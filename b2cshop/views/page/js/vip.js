$(document).ready(function(){
	//修改资料
	  $(".left-alter-info").click(function(){
		  $.get("/vip/user/info",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	//我的订单
	  $(".left-my-order").click(function(){
		  $.get("/vip/order/list",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	//我的评价
	  $(".left-my-rate").click(function(){
		  $.get("/item/rate/list",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	//收货地址
	  $(".left-my-add").click(function(){
		  $.get("/vip/address/list",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	//钱包
	  $(".left-my-purse").click(function(){
		  $.get("/vip/user/purse",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	//留言
	  $(".left-leave-mess").click(function(){
		  $.get("/messageboard/message/list",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	
});