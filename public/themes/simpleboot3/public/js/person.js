

$(function(){
	
	

	$(".add-invo a").click(function(){
	  	$(this).parents(".invo-exhibiting").hide();
	  	$(".invoceForm-list").show();
	});
	 
	$(".invo-cancel").click(function(){
		$(".invoceForm-list").hide();
		$(this).parents(".invoceForm-list").prev(".invo-exhibiting").show();
//		$(document.body).toggleClass("html_overflow");
	});
	
	//添加收货地址
	
	// $(".add-addr a").click(function(){
	//   	$(".changeAddress").show();
	//   	$(document.body).toggleClass("html_overflow");
	// });
	
	$(".addrCancel").click(function(){
		$(".changeAddress").hide();
		$(document.body).toggleClass("html_overflow");
	});
	 
});
 
// 更改地址和快递方式触发运费变化
function freight_count() {
	
}








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