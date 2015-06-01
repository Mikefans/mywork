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
  //类目添加
  $("#cate-add").click(function(){
	  $.get("/item/category/add",function(data,status){
		    $('.right-div').html(data);
		  });
	});
  
  //类目管理
  $("#cate-list").click(function(){
	  $.get("/item/category/list",function(data,status){
		    $('.right-div').html(data);
		  });
	});
  //在售商品：
  $("#item-onsell").click(function(){
	  $.get("/item/item/lists?status=1",function(data,status){
		    $('.right-div').html(data);
		  });
	});
  //仓库商品：
  $("#item-depot").click(function(){
	  $.get("/item/item/lists?status=2",function(data,status){
		    $('.right-div').html(data);
		  });
	});
  //商品添加
  $("#item-add").click(function(){
	  $.get("/item/item/add",function(data,status){
		    $('.right-div').html(data);
		  });
	});
});

