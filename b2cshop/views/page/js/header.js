$(document).ready(function(){
  $(".menu").mouseover(function(){
	    $(this).css({"background": "blue"});
	    $(this).children("ul").show();
	  });
  $(".menu").mouseout(function(){
	    $(this).css({"background": "red"});
	    $(this).children("ul").hide();
  });
  
  $(".next-menu li").mouseover(function(){
	  $(this).css({"background": "red"});
	  $(this).show();
  });
  $(".next-menu li").mouseout(function(){
	  $(this).css({"background": "blue"});
	  $(this).show();
  });
});

