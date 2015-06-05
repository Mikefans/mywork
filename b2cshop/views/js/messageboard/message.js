$(document).ready(function(){
	$(".commit-send").click(function(){
		var content = $(".mess-content").val();
		if (content == ''){
			ds.dialog({
				   title : '消息提示',
				   content : '内容不能为空',
				   timeout:2	  
				});
			return;
		}
		 $.post("/messageboard/message/save",
				  {
				    content:content,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '留言成功',
							   timeout:2	  
							});
						  $.get("/messageboard/message/list",
								  {
								  },
								  function(data,status){
									  $(".vip-right").html(data);
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
	
	$(".my-mess-div").click(function(){
		$.get("/messageboard/message/list",
				  {
				  },
				  function(data,status){
					  $(".vip-right").html(data);
				  });

	});
	$(".leave-mess-div").click(function(){
		
		$('.show-list').hide();
		$('.send-mess').show();
	});
	
	
	$(".to-reply").click(function(){
		var messageId = $('.to-reply').parent('li').attr('id');
		$.get("/messageboard/message/reply",
				  {
					message_id:messageId
				  },
				  function(data,status){
					  $(".vip-right").html(data);
				  });
	});
	
	$(".del-mess").click(function(){
		var messageId = $('.to-reply').parent('li').attr('id');
		var p = $('.current').html();
		$.get("/messageboard/message/del",
				  {
					message_id:messageId,
					page_num : p,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '删除成功',
							   timeout:2	  
							});
						  $.get("/messageboard/message/list",
								  {
								  },
								  function(data,status){
									  $(".vip-right").html(data);
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
	$(".send-reply").click(function(){
		$('.send-replys').show();
	});
	
	$(".commit-reply").click(function(){
		var content = $(".reply-content").val();
		if (content == ''){
			ds.dialog({
				   title : '消息提示',
				   content : '内容不能为空',
				   timeout:2	  
				});
			return;
		}
		 $.post("/messageboard/message/replysave",
				  {
				    content:content,
				    message_id : messageId,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '回复成功',
							   timeout:2	  
							});
						  $.get("/messageboard/message/reply",
								  {
							  		message_id:messageId,
								  },
								  function(data,status){
									  $(".vip-right").html(data);
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
	
});