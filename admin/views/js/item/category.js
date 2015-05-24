$(document).ready(function(){
  $("#demo1").click(function(){
		ds.dialog({
		   title : '消息提示',
		   content : '你好，欢迎访问A5源码JHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH！',
		   timeout:2	  
		});
	});

  //增加一个二级目录
  $(".add-second-input").click(function(){
	  var len = $(".cate-second-ul li").length;
	  var input ="<li class =li-num"+len+"><input type=text id=cate-second"+len+" name=cate_second"+len+"></li>";
	  $(".cate-second-ul").append(input); 
	});
//删除一个二级目录
  $(".del-second-input").click(function(){
	  var len = $(".cate-second-ul li").length-1;
	  $(".li-num"+len).remove();
	});
  //提交保存
  $(".cate-sumit").click(function(){
	  var cate = $('#cate-first').val();
	  if (cate == ''){
		  ds.dialog({
     		   title : '消息提示',
     		   content : '内容不能为空',
     		   timeout:2	
   	      });
		  return;
	  }
	  $.ajax({
          cache: true,
          type: "POST",
          url:'/item/category/addcate',
          data:$('#cate-form').serialize(),
          async: false,
          error: function(request) {
        	  ds.dialog({
          		   title : '消息提示',
          		   content : '服务错误，请重试',
          		   timeout:2	
        	  });
          },
          success: function(data) {
              if (data.status == 'success'){
            	  ds.dialog({
           		   title : '消息提示',
           		   content : '添加成功',
           		   timeout:2	  
           		});
              }else{
            	  ds.dialog({
           		   title : '消息提示',
           		   content : '服务错误，请重试',
           		   timeout:2	  
           		});
              }
            
          }
      });
	});
  
  $(".cate-list-second").click(function(){
	  $(".cate-list-li ul").hide();
	  $(this).parent("li").children("ul").show();
	});
  
 //增加一个二级目录
  $(".cate-list-second-add").click(function(){
	  $(this).parent("li").children("ul").show();
	  var id = $(this).parent("li").attr("id");
	  var input ="<li id="+id+"><input type=text id=cate-new-input /><span class=cate-list-del id='' >删除此类目</span></li>";
	  $(this).parent("li").children("ul").append(input); 
	});
  //修改一级类目
  $(".father-cate").blur(function(){
	  var cateId = $(this).parent("li").attr('id');
	  var cateName = $(this).val();
	  var cateNames = $(this).attr('value');
	  if (cateNames === cateName){
		  return;
	  }
	  $.post("/item/category/altercate",
			  {
			    cate_id:cateId,
			    cate_name:cateName
			  },
			  function(data,status){
				  if (status == 'success'){
					  if(data.status == 'error'){
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : data.desc,
			           		   timeout:2	  
			           		});
					  }else if (data.status == 'success'){
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : "修改成功",
			           		   timeout:2	  
			           		});
					  }
				  }else {
					  ds.dialog({
		           		   title : '消息提示',
		           		   content : "服务错误，请重试",
		           		   timeout:2	  
		           		});
				  }
			  });
	});
  //修改二级类目
  $(".next-cate").blur(function(){
	  var cateId = $(this).attr('id');
	  var fcateId = $(this).parent("li").attr("id");
	  var cateNames = $(this).attr('value');
	  var cateName = $(this).val();
	  if (cateNames == cateName){
		  return;
	  }
	  $.post("/item/category/altercate",
			  {
			    cate_id:fcateId,
			    cate_name:cateName,
			    next_cate_id:cateId
			  },
			  function(data,status){
				  if (status == 'success'){
					  if(data.status == 'error'){
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : data.desc,
			           		   timeout:2	  
			           		});
					  }else if (data.status == 'success'){
						  alert(1);
						  if ( isInt(data.result.id)){
							  $(this).attr("id",data.result.id);
						  }
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : "修改成功",
			           		   timeout:2	  
			           		});
					  }
				  }else {
					  ds.dialog({
		           		   title : '消息提示',
		           		   content : "服务错误，请重试",
		           		   timeout:2	  
		           		});
				  }
			  });
	});
  
  //保存 一个新增
  $(".save-list-new").click(function(){
	  var cateId = $(this).parent("li").attr("id");
	  var cateName = $("#cate-new-input").val();
	  $.post("/item/category/altercate",
			  {
			    cate_id:fcateId,
			    cate_name:cateName,
			    flag:1
			  },
			  function(data,status){
				  if (status == 'success'){
					  if(data.status == 'error'){
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : data.desc,
			           		   timeout:2	  
			           		});
					  }else if (data.status == 'success'){
						  alert(1);
						  if ( isInt(data.result.id)){
							  $(this).attr("id",data.result.id);
						  }
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : "修改成功",
			           		   timeout:2	  
			           		});
					  }
				  }else {
					  ds.dialog({
		           		   title : '消息提示',
		           		   content : "服务错误，请重试",
		           		   timeout:2	  
		           		});
				  }
			  });
	});
  
//删除一个类目
  $(".cate-list-del").click(function(){
	  var cateId = $(this).attr('id');
	  var fcateId = $(this).parent("li").attr("id");
	  $(this).parent("li").hide();return;
	  $.post("/item/category/altercate",
			  {
			    cate_id:fcateId,
			    cate_name:cateName,
			    next_cate_id:cateId
			  },
			  function(data,status){
				  if (status == 'success'){
					  if(data.status == 'error'){
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : data.desc,
			           		   timeout :2	  
			           		});
					  }else if (data.status == 'success'){
						  ds.dialog({
			           		   title : '消息提示',
			           		   content : "修改成功",
			           		   timeout :2	  
			           		});
					  }
				  }else {
					  ds.dialog({
		           		   title : '消息提示',
		           		   content : "服务错误，请重试",
		           		   timeout:2	  
		           		});
				  }
			  });
	});
  
});


