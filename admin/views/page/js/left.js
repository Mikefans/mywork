$(document).ready(function(){
  $("#demo1").click(function(){
		ds.dialog({
		   title : '消息提示',
		   content : '你好，欢迎访问A5源码JHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH！',
		   timeout:2	  
		});
	});

  
  $(".menu-li").click(function(){
	  $(".next-menu").hide();
	  $(this).children(".next-menu").show();
	});
  
  $("#cate-add").click(function(){
	  $.get("/item/category/add",function(data,status){
		    $('.right-div').html(data);
		  });
	});
  
  $("#cate-list").click(function(){
	  $.get("/item/category/list",function(data,status){
		    $('.right-div').html(data);
		  });
	});
});

