$(document).ready(function(){
	 $(".commit-info-alter").click(function(){
		 var userName = $('.user_name').val();
		 var userPasswd = $(".user_passwd").val();
		 var userMobile = $(".user_mobile").val();
		 var userEmail = $(".user_email").val();
		  $.get("/vip/user/alter",
				  {
			  			user_name:userName,
			  			user_passwd:userPasswd ,
			  			user_mobile: userMobile,
			  			user_email:userEmail,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '修改成功',
							   timeout:2	  
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
							   content : '服务错误，请刷新重试',
							   timeout:2	  
							});
					  }
					  
				  });
	  });
	
	
	
});