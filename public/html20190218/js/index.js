$(function(){
	// 加载公共底部
$("#footer").load("foot.html");
$("#bottomFooter").load("../bottom_footer.html");


// 侧导航切换
$(".allNav-lv1").mouseover(function(){
	var index = $(this).index();
	$(this).find(".allNav-lv2").show().end().addClass("current").siblings().removeClass("current");
});
$(".allNav-lv1").mouseout(function(){
	$(this).find(".allNav-lv2").hide();
	$(this).removeClass("current");

});
// 导航二级菜单
$(".nav_menu ul li.drop_menu").click(function(){
	$(this).find(".item_lists").toggle();
});
$(".item_lists a").click(function(){
	$(this).addClass("active").siblings().removeClass("current");
});
//被动选型侧导航的二级导航
$(".proNav .proNav_cap").click(function(){
	$(this).next(".proNav_div").toggle().parent().siblings().children(".proNav_div").css("display","none");
});

//选择交期
$(".steps_num-choice>div").click(function(){
	$(this).addClass("selected").siblings().removeClass("selected");
});
	    
	    
	    
//大陆订单页面
$(".newBtn").click(function(){
	$(".groupList-dsc").hide().prev(".groupList-grid").show();
});
$(".orderdelive div").click(function(){
	var index = $(this).index();
	$(this).addClass("ordeliveCurrt").siblings("div").removeClass("ordeliveCurrt");
	  			
	$(".orderdelive-cont>div").eq(index).addClass("storeCurrent").siblings().removeClass("storeCurrent")
});
$(".groupList-paylimit ul li").click(function(){
	$(this).addClass("shopchoice").siblings().removeClass("shopchoice");
});
$(".groupList-payway ul li").click(function(){
	$(this).addClass("shopchoice").siblings().removeClass("shopchoice");
});
$(".orderinvo div").click(function(){
	var index = $(this).index();
	$(this).addClass("ordeliveCurrt").siblings("div").removeClass("ordeliveCurrt");
	$(".orderinvo-group .orderinvo-group-list").eq(index).addClass("cateinvocurrent").siblings("div").removeClass("cateinvocurrent");
	  			
});
$(".invocheckchoice ul li").click(function(){
	var index = $(this).index();
	$(this).addClass("catecurrt").siblings("li").removeClass("catecurrt");
	$(".invocate-box>div").eq(index).addClass("cateinvocurrent").siblings("div").removeClass("cateinvocurrent");
});
	  		
$(".togginfo").click(function(){
	$(".groupbox-display").toggle();
});
$(".excultxt ul li").click(function(){
	$(this).addClass("excultxtaddr").siblings("li").removeClass("excultxtaddr");
});
$(".exaddinvo>button").click(function(){
	$(this).parents(".excultxt").hide();
	$(".exculform").show();
});
$(".invocateAdd>button").click(function(){
	$(this).parents(".invocateform-total").hide();
	$(this).parents(".invocateform-total").next(".invocatedisplayform").show();
});
$(".invocateform-total>ul>li").click(function(){
	$(this).addClass("incocateaddr").siblings("li").removeClass("incocateaddr");
});
	// 表格点击收起展开  		
$(".orderInfo-discount .infotext").click(function(){
	$(".orderInfodis-list").toggle();
	$(this).children("i").toggleClass("icon-icon icon-jiantou-copy");
});
//专用发票点击取消
$(".excuBtn>.callOff").click(function(){
	$(this).parents(".exculform").hide();
	$(".excultxt").show();
});
// 普通发票点击取消按钮
$(".personBtn>.callOff").click(function(){
	$(this).parents(".invocatedisplayform").hide();
	$(this).parents(".invocatedisplayform").prev(".invocateform-total").show();
});





























var t = $("#text_box");
$("#add").click(function(){
t.val(parseInt(t.val())+1)
setTotal();
})
$("#min").click(function(){
t.val(parseInt(t.val())-1)
setTotal();
})
function setTotal(){
var tt = $("#text_box").val();
var pbinfoid=$("#pbinfoid").val();
if(tt<=0){
alert('输入的值错误！');
t.val(parseInt(t.val())+1)
}else{
window.location.href = "shopping!updateMyCart.action?pbinfoid="+pbinfoid+"&number="+tt;
}
}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
});

// 购买数量加
function plus(tag,sellprice,stock){
	var _this=$(tag);
	var input=_this.prev('input');
	if(_this.prev('input[disabled]').length>0){
		return;
	}
	var amount=parseInt(input.val());
	amount++;
	if(amount>stock){
		return layer.msg('购买数量不能大于库存');
	}else{
		input.val(amount);
//		setTotal();
	}
}
// 购买数量减
 function minus(tag,sellprice,stock){
	var _this=$(tag);
	var input=_this.next('input');
	if(_this.next('input[disabled]').length>0){
		return;
	}
	var amount=parseInt(input.val());
		amount--;
	if(amount<=0){
		return layer.msg('购买数量不能小于1');
	}else{
		input.val(amount);
//	setTotal();
	}
}













