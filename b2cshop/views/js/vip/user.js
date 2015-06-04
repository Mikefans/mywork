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
	
	
	 $(".in-money").click(function(){
		 $(".out-money-div").hide();
		 $(".in-money-div").show();
		
	 });
	 
	 $(".out-money").click(function(){
		 $(".in-money-div").hide();
		 $(".out-money-div").show();
	 });
	 //充值：
	 $(".in-commit").click(function(){
		 var inMoney = $(".in-money-num").val();
		 
		 $.get('/vip/user/inmoney',{
				in_money:inMoney,  
				},
			function(data){
			    if (data.status=='success'){
			    	ds.dialog({
						   title : '消息提示',
						   content : '充值成功',
						   timeout:1
						});	
			    	$.get('/vip/user/purse',{
		    			},
		    		function(data){
		    		     $('.vip-right').html(data);	
		    		});
			    	$(".money-now").html(data.result.money);
			    }else if (data.status == 'error'){
			    	ds.dialog({
						   title : '消息提示',
						   content : data.desc,
						   timeout:1
						});	
			    }else{
			    	ds.dialog({
						   title : '消息提示',
						   content : '操作失败',
						   timeout:1
						});	
			    }
			});
	 });
	 
	//提现：
	 $(".out-commit").click(function(){
		 var outMoney = $(".out-money-num").val();
		 if (outMoney > money){
			 ds.dialog({
				   title : '消息提示',
				   content : '余额不足哦，亲',
				   timeout:1
				});	
		 }
		 $.get('/vip/user/outmoney',{
				out_money:outMoney,  
				},
			function(data){
			    if (data.status=='success'){
			    	ds.dialog({
						   title : '消息提示',
						   content : '提现成功',
						   timeout:1
						});	
			    	$.get('/vip/user/purse',{
		    			},
		    		function(data){
		    		     $('.vip-right').html(data);	
		    		});
			    	$(".money-now").html(data.result.money);
 
			    }else if (data.status == 'error'){
			    	ds.dialog({
						   title : '消息提示',
						   content : data.desc,
						   timeout:1
						});	
			    }else{
			    	ds.dialog({
						   title : '消息提示',
						   content : '操作失败',
						   timeout:1
						});	
			    }
			});
	 });
});