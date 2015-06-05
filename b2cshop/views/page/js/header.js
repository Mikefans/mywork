$(document).ready(function(){
  $(".menu").mouseover(function(){
	    $(this).css({"background": "#2875F4"});
	    $(this).children("ul").show();
	  });
  $(".menu").mouseout(function(){
	    $(this).css({"background": "#DC2233"});
	    $(this).children("ul").hide();
  });
  
  $(".next-menu li").mouseover(function(){
	  $(this).css({"background": "#DC2233"});
	  $(this).show();
  });
  $(".next-menu li").mouseout(function(){
	  $(this).css({"background": "#2875F4"});
	  $(this).show();
  });
  //登陆页面
  $(".head-login").click(function(){
		 location.href='/verify/public/login';
  });
  
  $("#demo1").click(function(){
		ds.dialog({
		   title : '消息提示',
		   content : '你好，欢迎访问A5源码JHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH！',
		   timeout:2	  
		});
	});
  
  
  $(".span-li").click(function(){
	  var id = $(this).parent('li').attr('id');
		 $.post("/item/item/lists",
				  {
				    cate_id:id,
				  },
				  function(data,status){
					  $('.body-divs').html(data);
				  });
	});
  
  $(".span-li-li").click(function(){
	  var id = $(this).parent('li').attr('id');
		 $.post("/item/item/lists",
				  {
				    cate_next:id,
				  },
				  function(data,status){
					  $('.body-divs').html(data);
				  });
	});
  
//返回首页
  $("#left-home").click(function(){
		 location.href='/home/index/index';
  });
//返回首页
  $("#head-home").click(function(){
		 location.href='/home/index/index';
  });
//退出登录
  $(".logout").click(function(){
	  $.get("/verify/public/logout",
			  {
			  },
			  function(data,status){
				  location.href='/home/index/index';
			  });
  });
//会员中心
  $(".vip").click(function(){
	  $.get("/vip/user/info",
			  {
			  },
			  function(data,status){
				  if (data.status == 'error')
				{
					  alert(data.desc);
					 
				}else{
					 location.href='/vip/index/index';
				}
			  });
	
  });
  
//购物车
  $(".car").click(function(){
	  $.get("/vip/user/info",
			  {
			  },
			  function(data,status){
				  if (data.status == 'error')
				{
					  alert(data.desc);
					 
				}else{
					 location.href='/vip/index/index';
				}
			  });
	
  });
});

