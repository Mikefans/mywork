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
});

