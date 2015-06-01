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
	
	//条件搜索：
	$(".condition-search").click(function(){
		var cateFather = $("#select-father").val();
		var cateNext = $("#select-next").val();
		var q = $("#key-input").val();
		$.get('/item/item/list',{
			status : itemStatus,
			cate_father:cateFather,
			cate_next: cateNext,
			q:q
			},
		function(data){
			if (data.status == 'success'){
				 $("#item-list-table tr").remove();
				    var title = "<tr><td>商品ID</td><td>商品标题</td><td>价格</td><td>优惠价</td><td>邮费</td><td>是否推荐</td><td>库存</td><td>操作</td></tr>";
				    $("#item-list-table").append(title);
				    if (data.result.data == ''){
				    	$(".tcdPageCode").createPage({
					        pageCount:0,
					        current:1,
					        backFn:function(p){
					        }
					    });
				    	return;
				    }
				    var datas = data.result.data;
				    var len = datas.length;
				    for (var i = 0 ; i < len ; i++){
				    	if (datas[i].is_recommend == 1){
				    		var isRecommend = '已推荐';
				    		var recommendOp = '取消推荐';
				    		var opId = -2;
				    	}else {
				    		var isRecommend = '未推荐';
				    		var recommendOp = '立即推荐';
				    		var opId = 2;
				    	}
				    	if (itemStatus == 1){
				    		var isOnsell = '下架';
				    	}else {
				    		var isOnsell = '上架';
				    	}
				    	var td =  "<tr><td><input type=checkbox value="+datas[i].item_id+" />"+datas[i].item_id+"</td><td>"+datas[i].item_title+"</td><td>"+datas[i].item_price+"</td><td>"+datas[i].item_promo_price+"</td>"+
				        "<td>"+datas[i].item_post+"</td><td id=op"+datas[i].item_id+" >"+isRecommend+"</td><td>"+datas[i].item_total+"</td>"+
				        "<td><ul class=item-operator id= "+datas[i].item_id+"><li id=1>删除</li><li id="+opId+" >"+recommendOp+"</li><li id=3 >"+isOnsell+"</li></ul></td></tr>";
				    	$("#item-list-table").append(td);
				    }
				    
					$(".tcdPageCode").createPage({
				        pageCount:data.result.page_count,
				        current:1,
				        backFn:function(p){
				        }
				    });
			}else if(data.status == 'error'){
				ds.dialog({
					   title : '消息提示',
					   content : data.desc,
					   timeout:2	  
					});
			}else{
				ds.dialog({
					   title : '消息提示',
					   content : '服务错误请重试',
					   timeout:2	  
					});
			}
		   
		});
	});
	
	//商品操作
	$(document).on("click", ".item-operator li", function (){
//	$(".item-operator li").click(function(){
		var types = $(this).attr('id');
		var itemId = $(this).parent("ul").attr("id");
		if (types == 1 || types ==3){
			$(this).parent("ul").parent("td").parent("tr").hide();
		}else {
			var opId =  "op"+itemId;
			if (types == 2){
				$(this)[0].innerHTML="取消推荐";
				$("#"+opId+"")[0].innerHTML="已推荐";
				$(this).attr('id',-2);
			}else{
				$(this)[0].innerHTML="立即推荐";
				$("#"+opId+"")[0].innerHTML="未推荐";
				$(this).attr('id',2);
			}
		}	
		$.get('/item/item/alter',{
			status : itemStatus,
			type:types,
			item_id:itemId,
			},
		function(data){
			if (data.status == 'success'){
				
				ds.dialog({
					   title : '消息提示',
					   content : '操作成功',
					   timeout:1
					});	
			}else if (data.status == 'error'){
				ds.dialog({
					   title : '消息提示',
					   content : data.desc,
					   timeout:2	  
					});
			}else{
				ds.dialog({
					   title : '消息提示',
					   content : '服务错误请重试',
					   timeout:2	  
					});
			}
		});
	});
	
	
});

