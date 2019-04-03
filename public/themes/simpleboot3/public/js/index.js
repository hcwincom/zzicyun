$(function(){
// 顶部广告点击删除
	$(".banner-wrap>span").click(function () {
		$(".banner-wrap").hide();
	});


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
	    	    
//大陆订单页面

	$(".callOffAddr").click(function () {
		$(".groupList-grid").hide();
		$(document.body).toggleClass("html_overflow");
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
// $(".invocheckchoice ul li").click(function(){
// 	var index = $(this).index();
// 	$(this).addClass("catecurrt").siblings("li").removeClass("catecurrt");
// 	$(".invocate-box>div").eq(index).addClass("cateinvocurrent").siblings("div").removeClass("cateinvocurrent");
// });
	  		
// $(".togginfo").click(function(){
// 	$(".groupbox-display").toggle();
// });
$(".excultxt ul li").click(function(){
	$(this).addClass("excultxtaddr").siblings("li").removeClass("excultxtaddr");
});
$(".exaddinvo>button").click(function(){
	// $(this).parents(".excultxt").hide();
	$(".exculform").show();
	$(document.body).toggleClass("html_overflow");
});
	$(".callOffInvoce").click(function () {
		$(".exculform").hide();
		$(document.body).toggleClass("html_overflow");
	});

	$(".invocateAdd>.cate-addbtn").click(function () {
		$(".invocatedisplayform").fadeIn();
		$(document.body).toggleClass("html_overflow");
	});
	$(".callOffCom").click(function () {
		$(".invocatedisplayform").fadeOut();
		$(document.body).toggleClass("html_overflow");
	});
	$(".personInvoAdd>.person_addbtn").click(function () {
		$(".invoCatePersonForm").fadeIn();
		$(document.body).toggleClass("html_overflow");
	});
	$(".callOffPerson").click(function () {
		$(".invoCatePersonForm").fadeOut();
		$(document.body).toggleClass("html_overflow");
	});











// $(".invocateAdd>button").click(function(){
// 	$(this).parents(".invocateform-total").hide();
// 	$(this).parents(".invocateform-total").next(".invocatedisplayform").show();
// });
// $(".invocateform-total>ul>li").click(function(){
// 	$(this).addClass("incocateaddr").siblings("li").removeClass("incocateaddr");
// });
	// 表格点击收起展开  		
// $(".orderInfo-discount .infotext").click(function(){
// 	$(".orderInfodis-list").toggle();
// 	$(this).children("i").toggleClass("icon-icon icon-jiantou-copy");
// });

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

//  订单页面选择优惠券





// 门店选择
	$(".storeAddfr>li").click(function () {
		$(this).addClass("storeActive").siblings("li").removeClass("storeActive");
	});




























	
	
	
	
});






// 商品详情页面的价格变化
function setTotal(){
	var allPrice = 0; // 总价格
	$(".stepItem").each(function(){
		var singleNum = parseInt($(".totalNum").val());   //单个商品的购买数量
		var stepNum = parseInt($(this).find("span").text());
		if(singleNum >= stepNum){
			var singlePrice = parseFloat($(this).next("td").find("span").text());
			// console.log(singlePrice);
			$(".detail_pri").find("i").text(singlePrice);
			allPrice = singleNum*singlePrice;
		}
		$(".allPrice").html(allPrice.toFixed(2));
	});
	
}
setTotal();



//手动修改文本框商品数量与库存的限制
function amount_input(tag,sellprice,stock){
	var amount=parseInt($(tag).val());
	if(isNaN(amount)){
		alert('最少购买量为1');
		$(tag).val(1);
	}else{
		if(amount>stock){
			alert('购买数量不能大于库存');
			$(tag).val(stock);
		}else if(amount<1){
			alert('最少购买量为1');
			$(tag).val(1);
		}
	}
	var val=parseFloat(sellprice)*parseInt($(tag).val());
	setTotal();
}

// 购买数量加
function plus(tag,sellprice,stock){
	var _this=$(tag);
	var input=_this.prev('input');
	var amount=parseInt(input.val());
	amount++;
	if(amount>stock){
		alert('购买数量不能大于库存');
	}else{
		input.val(amount);
		setTotal();
	}
}
// 购买数量减
 function minus(tag,sellprice,stock){
	var _this=$(tag);
	var input=_this.next('input');
	var amount=parseInt(input.val());
		amount--;
	if(amount<=0){
		return alert('购买数量不能小于1');
	}else{
		input.val(amount);
		setTotal();
	}
}
 



// 购物车页面订单结算
function setTotalScart(){
	$(".shopTable tr.shopdetail").each(function(){
		var scartPrice =$(this).children(".scartPrice").find("span").text();
		 	scartNum = $(this).find(".onum").val();
	
		var smallAmount = scartPrice*scartNum;
		$(this).find(".subtotal span").html(smallAmount.toFixed(2));
	});
	
}	
setTotalScart();
// 单选
$(".shop-check").click(function(){
	var check=$(".shop-check").length; 
	console.log(check);
	var checked=$(".shop-check:checked").length; 
	console.log(checked);
	if(check==checked){ 
		$("#checkAll").prop("checked",true);
	}else{ 
		$("#checkAll").prop("checked",false);
	} 	
	checkNum();	
	allPrice();
});		

// 购物车全选
$("#checkAll").click(function(){
	if(this.checked){
		$(".shop-check").prop("checked",true);
	}else{
		$(".shop-check").prop("checked",false);
	}
	checkNum();	
	allPrice();
	
});


//购物车订单页面手动修改文本框商品数量与库存的限制
function amount_input_Scart(tag,sellprice,stock){
	var amount=parseInt($(tag).val());
	if(isNaN(amount)){
		alert('最少购买量为1');
		$(tag).val(1);
	}else{
		if(amount>stock){
			alert('购买数量不能大于库存');
			$(tag).val(stock);
		}else if(amount<1){
			alert('最少购买量为1');
			$(tag).val(1);
		}
	}
	setTotalScart();
	checkNum();
	allPrice();
}
// 数量增加
function plusScart(tag,sellprice,stock){
	var _this=$(tag);
	var input=_this.prev('input');
	var amount=parseInt(input.val());
	amount++;
	if(amount>stock){
		alert('购买数量不能大于库存');
	}else{
		input.val(amount);
		setTotalScart();
		checkNum();
		allPrice();
	}
}
// 数量减少
function minusScart(tag,sellprice,stock){
	var _this=$(tag);
	var input=_this.next('input');
	var amount=parseInt(input.val());
		amount--;
	if(amount<=0){
		return alert('购买数量不能小于1');
	}else{
		input.val(amount);
		setTotalScart();
		checkNum();
		allPrice();
	}
}
// 购买总数量
function checkNum(){
	var num = 0;
	$(".shop-check").each(function(){
		if(this.checked==true){ 
			var snum = parseFloat($(this).parents("tr").find(".onum").val());
			num+=Number(snum.toFixed(2)); 
		} 
	});
	$(".allNum").html(num);
}
function allPrice(){ 
	var sum=0;  // 总和
	$(".shop-check").each(function(){ 
		if(this.checked==true){
			var ssum = parseFloat($(this).parents("tr").find(".subtotal span").text());
			sum+=Number(ssum); 
		} 
	}); 
	$(".allPrice1").html(sum.toFixed(2)); 
}



// 商品详情页加入购物车计算
$("input[name='checkra']").click(function(){
	var _this = $(this);
	var checked = $("input[name='checkra']:checked").val();
	var count = _this.parents(".detail_form").find(".de_onum").val();
	console.log(count);
	if(checked == "checkrad"){
		$(".priceRmb").hide().next(".priceUsd").show();
		$(".totalRmb").hide().next(".totalUsd").show();

		$(".stepItem").each(function(){
			var stepNum = parseInt($(this).find("span").text());
			if(count >= stepNum){
				var usdPrice =  parseFloat($(this).next("td").next("td").find("span").text());
				$(".detail_pri").find(".priceUsd i").text(usdPrice.toFixed(2));
				allPrice = count * usdPrice;
				$(".allPrice").html(allPrice.toFixed(2));
			}
		});
	}else{
		$(".priceUsd").hide().prev(".priceRmb").show();
		$(".totalUsd").hide().prev(".totalRmb").show();
		$(".stepItem").each(function(){
			var stepNum = parseInt($(this).find("span").text());
			if(count >= stepNum){
				var singlePrice = parseFloat($(this).next("td").find("span").text());
				$(".detail_pri").find(".priceRmb i").text(singlePrice.toFixed(2));
				allPrice = count * singlePrice;
				$(".allPrice").html(allPrice.toFixed(2));
			}
		});
	}
});

$(".de_oplus").click(function () {
	var checked = $("input[name='checkra']:checked").val();
	var count=$(this).prev("input").val();
	// 最小数量
	var lessNum = parseInt($(".lessNum").find("span").text());  
	// 倍数
	var diploidNum = parseInt($(".diploid").find("span").text());  
	count=parseInt(count)+diploidNum;
	$(this).prev("input").val(count);
	//计算价格
	$(".stepItem").each(function () {
		// 阶梯价格
		var stepNum = parseInt($(this).find("span").text());
		if (count >= stepNum) {
			if(checked == "checkra"){
				$(".priceUsd").hide().prev(".priceRmb").show();
				$(".totalUsd").hide().prev(".totalRmb").show();
				// 阶梯单价
				var singlePrice = parseFloat($(this).next("td").find("span").text());
			   $(".detail_pri").find(".priceRmb i").text(singlePrice.toFixed(2));
			   allPrice = count * singlePrice;
			   $(".allPrice").html(allPrice.toFixed(2));
			}else{
				$(".priceRmb").hide().next(".priceUsd").show();
				$(".totalRmb").hide().next(".totalUsd").show();
				var usdPrice =  parseFloat($(this).next("td").next("td").find("span").text());
				$(".detail_pri").find(".priceUsd i").text(usdPrice.toFixed(2));
				console.log(usdPrice);
				allPrice = count * usdPrice;
				$(".allPrice").html(allPrice.toFixed(2));
			}
		}
		
	});
});

$(".de_omin").click(function(){
	var checked = $("input[name='checkra']:checked").val();
	// 获取数量
	var count=parseInt($(this).next("input").val());
	console.log(count);
	// 最小数量
	var lessNum = parseInt($(".lessNum").find("span").text());  
	console.log(lessNum);
	if(count===lessNum){
		return false;
	}
	// 倍数
	var diploidNum = parseInt($(".diploid").find("span").text());  
	count = parseInt(count)-diploidNum;
	$(this).next("input").val(count);
	//计算价格
	$(".stepItem").each(function () {
		// 阶梯价格
		var stepNum = parseInt($(this).find("span").text());
		if (count >= stepNum) {
			if(checked == "checkra"){
				$(".priceUsd").hide().prev(".priceRmb").show();
				$(".totalUsd").hide().prev(".totalRmb").show();
				// 阶梯单价
				var singlePrice = parseFloat($(this).next("td").find("span").text());
			   $(".detail_pri").find(".priceRmb i").text(singlePrice.toFixed(2));
			   allPrice = count * singlePrice;
			   $(".allPrice").html(allPrice.toFixed(2));
			}else{
				$(".priceRmb").hide().next(".priceUsd").show();
				$(".totalRmb").hide().next(".totalUsd").show();
				var usdPrice =  parseFloat($(this).next("td").next("td").find("span").text());
				$(".detail_pri").find(".priceUsd i").text(usdPrice.toFixed(2));
				console.log(usdPrice);
				allPrice = count * usdPrice;
				$(".allPrice").html(allPrice.toFixed(2));
			}
		}
		
	});
});


// 被动选型页面的价格和数量变化

//选择交期
$(".steps_num-choice>div").click(function(){
	var _this = $(this);
	$(this).addClass("selected").siblings().removeClass("selected");
	var selected = $(".selected").attr("data-value");
	var count = _this.parents("tr").find(".orderNum").val();
	if(selected == "HK"){
		$(this).parents("tr").find(".padStep .totalStep").find(".stepsNumber").each(function(){
			var stepNum = parseInt($(this).find("span").text());
		
			if (count >= stepNum) {
				$(this).parents("tr").find(".totalUsd").show().prev(".totalRmb").hide();
				var priceUsd = parseFloat($(this).next(".steps_price").next(".steps_doller").find("span").text());
					
				totalNum = count*priceUsd;
				$(this).parents("tr").find(".totalUsd").children("span").text(totalNum.toFixed(2));
				
			}
			
		});
	}else{
		$(this).parents("tr").find(".padStep .totalStep").find(".stepsNumber").each(function(){
			var stepNum = parseInt($(this).find("span").text());

			if (count >= stepNum) {
				$(this).parents("tr").find(".totalRmb").show().next(".totalUsd").hide();
				var singlePrice = parseFloat($(this).next(".steps_price").find("span").text());
									
				totalNum = count*singlePrice;
			 	$(this).parents("tr").find(".totalRmb").children("span").text(totalNum.toFixed(2));
								
			}
		});
	}


});

// 数量增加
var totalNum ;
$(".go_plus").click(function(){
	// 选中交货地
	var selected = $(".selected").attr("data-value");
	//获取数量
	var count=$(this).prev("input").val();
	// 倍数
	var diploidNum = parseInt($(this).parents("tr").find(".number .diploid").children("span").text());
	count=parseInt(count)+diploidNum;
	$(this).prev("input").val(count);

	//计算价格
	$(this).parents("tr").find(".padStep .totalStep").find(".stepsNumber").each(function(){
			var stepNum = parseInt($(this).find("span").text());
		
			if (count >= stepNum) {

				if(selected == "CN"){
					$(this).parents("tr").find(".totalRmb").show().next(".totalUsd").hide();
					var singlePrice = parseFloat($(this).next(".steps_price").find("span").text());
					
					 totalNum = count*singlePrice;
					 $(this).parents("tr").find(".totalRmb").children("span").text(totalNum.toFixed(2));
				}else{
					$(this).parents("tr").find(".totalUsd").show().prev(".totalRmb").hide();
					var priceUsd = parseFloat($(this).next(".steps_price").next(".steps_doller").find("span").text());
					
					totalNum = count*priceUsd;
					$(this).parents("tr").find(".totalUsd").children("span").text(totalNum.toFixed(2));
				}
			}
			
		});

});
// 数量减少
$(".go_min").click(function(){
	// 选中交货地
	var selected = $(".selected").attr("data-value");
	//获取数量
	var count=$(this).next("input").val();
	var lessNum = $(this).parents("tr").find(".number .lessNum").children("span").text();
	if(count===lessNum){
		return false;
	}
	// 倍数
	var diploidNum = parseInt($(this).parents("tr").find(".number .diploid").children("span").text());
	count=parseInt(count)-diploidNum;
	$(this).next("input").val(count);

	//计算价格
	$(this).parents("tr").find(".padStep .totalStep").find(".stepsNumber").each(function(){
		
		var stepNum = parseInt($(this).find("span").text());
	
		if (count >= stepNum) {

			if(selected == "CN"){
				$(this).parents("tr").find(".totalRmb").show().next(".totalUsd").hide();
				var singlePrice = parseFloat($(this).next(".steps_price").find("span").text());
				
				 totalNum = count*singlePrice;
				 $(this).parents("tr").find(".totalRmb").children("span").text(totalNum.toFixed(2));
			}else{
				$(this).parents("tr").find(".totalUsd").show().prev(".totalRmb").hide();
				var priceUsd = parseFloat($(this).next(".steps_price").next(".steps_doller").find("span").text());
				
				totalNum = count*priceUsd;
				$(this).parents("tr").find(".totalUsd").children("span").text(totalNum.toFixed(2));
			}
						
		}
		
	});

});










