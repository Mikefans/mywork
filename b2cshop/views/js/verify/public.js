$(document).ready(function(){
  $(".verify-img").click(function(){
	  $(this).attr("src",function(){return this.src+"?"});
	  });
  
  //找回密码
  $('.forget-password').click(function(){
	  $(".login-div").slideUp();
	  $(".seapass-div").slideDown();
	 
  });
  //注册用户
  $('.register-free').click(function(){
	  $(".login-div").slideUp();
	  $(".register-div").slideDown();
	 
  });
  //返回登陆(注册页)
  $('.back-login').click(function(){
	  $(".register-div").slideUp();
	  $(".login-div").slideDown();
  });
//返回登陆(忘记密码)
  $('.rback-login').click(function(){
	  $(".login-div").slideDown();
	  $(".seapass-div").slideUp();
  });
  
  
  //用户提交注册
  $('.register-submit').click(function(){
	  
	  var userName = $("#user_name").val();
	  var user_email = $("#user_email").val();
	  var mobile = $("#user_mobile").val();
	  var password = $("#pass_word").val();
	  var passwords = $("#pass_words").val();
	  if ( userName =='' || user_email == '' || mobile== '' || password == ''){
		  ds.dialog({
			   title : '消息提示',
			   content : '以上内容不能为空',
			   timeout:1	  
			});
	  }else if (password != passwords){
		  ds.dialog({
			   title : '消息提示',
			   content : '两次输入的密码不一样',
			   timeout:2	  
			});
	  }else{
		  $.post("/verify/public/register",
				  {
				    user_name:userName,
				    mobile:mobile,
				    email : user_email,
				    pass_word : password,
				  },
				  function(data,status){
					  if (data.status == 'success'){
						  ds.dialog({
							   title : '消息提示',
							   content : '恭喜您，注册成功！请赶快前往登陆吧',
							   timeout:2	  
							});
						  
					  }else if (typeof JSON.parse(data) == 'object') {
						  ds.dialog({
							   title : '消息提示',
							   content : JSON.parse(data).desc,
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
	  }
  });
	  //用户提交登陆
	  $('.login-submit').click(function(){
		  var userName = $("#username").val();
		  var password = $("#password").val();
		  var code = $("#verifycode").val();
		  if ( userName =='' || password == '' || code == ''){
			  ds.dialog({
				   title : '消息提示',
				   content : '以上内容不能为空',
				   timeout:1	  
				});
		  }else{
			  $.post("/verify/public/logins",
					  {
					    user_name:userName,
					    pass_word : password,
					    verify_code : code
					  },
					  function(data,status){
						  if (data.status == 'success'){
							  history.back();
						  }else if (typeof JSON.parse(data) == 'object') {
							  ds.dialog({
								   title : '消息提示',
								   content : JSON.parse(data).desc,
								   timeout:2	  
								});
						  }else {
							  ds.dialog({
								   title : '消息提示',
								   content : '服务错误，请重试',
								   timeout:2	  
								});
						  }
					  });
		  }
  });
  
  $('.ur').blur(function(){

  });
  
});

