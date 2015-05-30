$(document).ready(function(){
	//下拉列表级联选择事件
	$("#select-father").change(function(){
		var cid = $(this).val();
		$.get("item/item/add?cid="+cid+"?",function(data,status){
		    if (data.status == 'success'){
		    	var arr = data.result;
		    	var len = arr.length;
		    	$("#select-next").empty();
		    	if (len == 0){
		    		var option = "<option value=0 >请选择一级类目</option>";
		    		$("#select-next").append(option);
		    	}else {
			    	for (var i =0; i<len ;i++){
			    		var option = "<option value="+arr[i].cate_id+">"+arr[i].cate_name+"</option>";
			    		$("#select-next").append(option);
			    	}
		    	}
		    }else {
		    	 ds.dialog({
	           		   title : '消息提示',
	           		   content : "服务错误，请刷新页面后重试",
	           		   timeout:2	  
	           		});
		    }
		  });
		
		
   });
	//上传图片(主图)
	$("#upload-main").click(function(){
		var data = {name:'upfileMain'};
		$.ajaxFileUpload({
	          type: "POST",
	          url:'/item/item/upload',
	          secureuri: false,
	          data:data,
	          fileElementId:'upfileMain',
	          dataType: 'json',
	          error:function(data) {
	        	  var tt = data.responseText.replace(/<.*?>/ig,"");
	        	  var obj = JSON.parse(tt);
	              if (obj.status == 'success'){
	            	  if (obj.result.state == 'SUCCESS'){
	            		  $("#main-img").attr('src',obj.result.url);
	            		  $("#main-img").show();
	            		  ds.dialog({
	   	           		   title : '消息提示',
	   	           		   content : '添加成功',
	   	           		   timeout:2	  
	   	           		});
	            	  }else{
	            		  ds.dialog({
	   	           		   title : '消息提示',
	   	           		   content : obj.result.state,
	   	           		   timeout:2	  
	   	           		});
	            	  }
	            	  
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
	
	//上传图片(缩略图)
	$("#upload-next").click(function(){
		var data = {name:'upfileNext'};
		$.ajaxFileUpload({
	          type: "POST",
	          url:'/item/item/upload',
	          secureuri: false,
	          data:data,
	          fileElementId:'upfileNext',
	          dataType: 'json',
	          error:function(data) {
	        	  var tt = data.responseText.replace(/<.*?>/ig,"");
	        	  var obj = JSON.parse(tt);
	              if (obj.status == 'success'){
	            	  if (obj.result.state == 'SUCCESS'){
	            		  var len = $(".next-img-ul li").length;
	            		  var imgs = "<li id="+obj.result.url+" class=img-li-li><img src="+obj.result.url+" class=image-next-li /></li>";
	            		  $(".next-img-ul").append(imgs);
	            		  ds.dialog({
	   	           		   title : '消息提示',
	   	           		   content : '添加成功',
	   	           		   timeout:2	  
	   	           		});
	            	  }else{
	            		  ds.dialog({
	   	           		   title : '消息提示',
	   	           		   content : obj.result.state,
	   	           		   timeout:2	  
	   	           		});
	            	  }
	            	  
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
	//删除一个缩略图
	$("ul").on("click",".img-li-li",function(){
		$(this).remove();
	});
	
	
	//保存商品
	$("#save-item-button").click(function(){
		var nextImg ='';
		var len = $(".next-img-ul li").length;
		for (var i = 0;i<len;i++){
			nextImg  = nextImg+","+$(".next-img-ul li").eq(i).attr('id');
		}
		var mainImg = $("#main-img").attr('src');
		var cateFather = $("#select-father").val();
		var cateNext = $("#select-next").val();
		var itemTitle = $("#item_title").val();
		var itemPrice = $("#item_price").val();
		var itemPromoPrice = $("#item_promo_price").val();
		var itemPost = $("#item_post").val();
		var itemTotal = $("#item_total").val();
		var itemColors = $("#item_color").val();
		var itemMarque = $("#item_marque").val();
		var description = UM.getEditor('myEditor').getContent();
		$.post("/item/item/adds",
				  {
				    item_title:itemTitle,
				    item_price:itemPrice,
				    item_promo_price: itemPromoPrice,
				    item_total: itemTotal,
				    item_post: itemPost,
				    item_color: itemColors,
				    item_marque: itemMarque,
				    item_description:description,
				    item_img:mainImg,
				    item_img_next:nextImg,
				    cate_id:cateFather,
				    cate_id_before:cateNext
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
				           		   content : "保存成功",
				           		   timeout:2	  
				           		});
						  }else{
							  ds.dialog({
				           		   title : '消息提示',
				           		   content : "服务错误，请重试",
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
	
});
