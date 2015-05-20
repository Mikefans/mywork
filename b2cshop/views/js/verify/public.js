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
  
});

