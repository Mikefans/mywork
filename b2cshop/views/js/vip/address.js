$(document).ready(function(){
	//bianji
	  $(".add-edit").click(function(){
		  var id = $(this).attr('id');
		  $("#add-show-id"+id).hide();
		  $("#add-hide-id"+id).show();
		  return;
		  $.get("/vip/user/info",
				  {
				  },
				  function(data,status){
					 $(".vip-right").html(data);
				  });
	  });
	//保存
	  $(".add-save").click(function(){
		  var id = $(this).attr('id');
		  var receiverMobile = $("#re-m").val();
		  var receiverName = $("#re-n").val();
		  var addressDetail = $("#re-d").val();
		  $.post("/vip/address/save",
				  {
			  		address_id : id,
			  		address_detail: addressDetail,
			  		receiver_mobile: receiverMobile,
			  		receiver_name : receiverName,
				  },
				  function(data,status){
					  ds.dialog({
						   title : '消息提示',
						   content : '保存成功',
						   timeout:2
						});	
					  $.get("/vip/address/list",
							  {
							  },
							  function(data,status){
								 $(".vip-right").html(data);
							  });
				  });
	  });
	
	//删除
	  $(".add-del").click(function(){
		  var id = $(this).attr('id');
		  $.post("/vip/address/del",
				  {
			  		address_id : id,
				  },
				  function(data,status){
					  ds.dialog({
						   title : '消息提示',
						   content :'删除成功',
						   timeout:2
						});	
					  $.get("/vip/address/list",
							  {
							  },
							  function(data,status){
								 $(".vip-right").html(data);
							  });
				  });
	  });
	
	//新增
	  $(".add-new").click(function(){
		  var receiverMobile = $("#res-m").val();
		  var receiverName = $("#res-n").val();
		  var addressDetail = $("#res-d").val();
		  $.post("/vip/address/new",
				  {
			  		address_detail: addressDetail,
			  		receiver_mobile: receiverMobile,
			  		receiver_name : receiverName,
				  },
				  function(data,status){
					  ds.dialog({
						   title : '消息提示',
						   content : '保存成功',
						   timeout:2
						});	
					  $.get("/vip/address/list",
							  {
							  },
							  function(data,status){
								 $(".vip-right").html(data);
							  });
				  });
	  });
	  
	//新增
	  $(".add-add-new").click(function(){
		  $(".shows").hide();
		  $(".hides").show();
	  });
	//取消
	  $(".add-cancle-new").click(function(){
		  
		  $(".hides").hide();
		  $(".shows").show();
	  });
});