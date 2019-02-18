var notice_json={
	'common':
			{'applay_name':'请输入联系人姓名','applay_phone':'请输入手机号码','apply_file':'文件已上传','apply_size':'上传的图片大小不能超过10M !',
			 'apply_companyName':'请输入公司名称','apply_email':'请输入可用邮箱','apply_read':'请确认已阅读入驻协议','apply_uploadfile':'请上传文件',
			 'job_name':'请输入姓名','job_code':"请输入验证码",'city_check':'请选择收货地址','detail_addr':'请填写详细地址','dafult_take':'设为默认',
			 'changePwd':'新旧密码不能相同！','changeNewPwd':'密码不一致！','oldPwd':'请输入旧密码','newPwd':'请输入新密码','newrePwd':'请再次输入新密码',
			},
	 
	};
console.log(notice_json.common.success);
// 验证6位密码
	function isPassword(password){
	    var pattern=/^\d{6}$/;
	    return pattern.test(password)
	}
	// 验证4位验证码
	function isCode(password){
	    var pattern=/^\d{4}$/;
	    return pattern.test(password)
	}
	// 验证中文名称
	function isChinaName(name) {
	    var pattern = /^[\u4E00-\u9FA5]{1,6}$/;
	    return pattern.test(name);
	}
	// 验证手机号码
	function isPhoneNo(phone) { 
	    var pattern = /^1[34578]\d{9}$/; 
	    return pattern.test(phone); 
	}
	// 验证邮箱
	function isValidEmailAddress(emailAddress) {
	    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
	    return pattern.test(emailAddress);
	}
	//招募供应商申请页面
	$("#trading").change(function(){
		var file_size = this.files[0].size;
		console.log(file_size);
        var size = file_size / 1024;
        if (size > 10240) {
        alert(notice_json.common.apply_size);
            return false;
        } 
		$(this).next("label").html(notice_json.common.apply_file);
	});
	$("#agency").change(function(){
		var file_size = this.files[0].size;
		console.log(file_size);
        var size = file_size / 1024;
        if (size > 10240) {
            alert(notice_json.common.apply_size);
            return false;
        } 
		$(this).next("label").html(notice_json.common.apply_file);
	});
	function verify(){
		var aname =$.trim($("#aname").val());
		var aiphone =$.trim($("#aiphone").val());
		var apname =$.trim($("#apname").val());
		var aemail =$.trim($("#aemail").val());
		var fileName = $("#trading").val();
		var file1 = $("#agency").val();
		if(aname == ""){
			$("#aname").focus();
			$(".apmsg").text(notice_json.common.applay_name);
			return false;
		}else if(aiphone == "" || isPhoneNo(aiphone) == false){
			$("#aiphone").focus();
			$(".apmsg1").text(notice_json.common.applay_phone);
			return false;
		}else if(apname == ""){
			$("#apname").focus();
			$(".apmsg2").text(notice_json.common.apply_companyName);
			return false;
		}else if(aemail == "" || isValidEmailAddress(aemail) == false){
			$("#aemail").focus();
			$(".apmsg3").text(notice_json.common.apply_email);
			return false;
		}else if(fileName == ""){
			$(".fileMsg").text(notice_json.common.apply_uploadfile);
			return false;
		}else if(file1 == ""){
			$(".file1Msg").text(notice_json.common.apply_uploadfile);
			return false;
		}else if($("#acheck").prop('checked') == false){
			$(".apmsg4").text(notice_json.common.apply_read);
			return false;
		}
		$(".ml").text("");
		return true;
	}

	//加入我们招聘页面
	$("#job-file").change(function(){
		$(this).next("label").html(notice_json.common.apply_file);
	});
	function jobfair(){
		var jobName = $.trim($("#job-name").val());
		var jobPhone = $.trim($("#job-phone").val());
		var jobEmail = $.trim($("#job-mail").val());
		var jobYan = $.trim($("#job-yan").val());
		var jobFile = $.trim($("#job-file").val());
		if(jobName == ""){
			$("#job-name").focus();
			$(".invite-msg").text(notice_json.common.job_name);
			return false;
		}else if(jobPhone == "" || isPhoneNo(jobPhone) == false){
			$("#job-phone").focus();
			$(".invite-msg").text(notice_json.common.applay_phone);
			return false;
		}else if(jobEmail == "" || isValidEmailAddress(jobEmail) == false){
			$("#job-mail").focus();
			$(".invite-msg").text(notice_json.common.apply_email);
			return false;
		}else if(jobYan == "" || isCode(jobYan) == false){
			$("#job-yan").focus();
			$(".invite-msg").text(notice_json.common.job_code);
			return false;
		}else if(jobFile == ""){
			$(".invite-msg").text(notice_json.common.apply_uploadfile);
			return false;
		}
		$(".invite-msg").text("");
		return true;
	}


	// 大陆提交订单页面
	$("#invo-file").change(function(){
		$(".box-input .invo-label").html(notice_json.common.apply_file);
	});
	
	$("#keepaddr").click(function(){
		var takeName = $.trim($("#takeName").val());
		var takePhone = $.trim($("#takePhone").val());
		var takeCity = $.trim($("#takeCity").val());
		var takeAddr = $.trim($("#takeAddr").val());
		if(takeName == ""){
			$("#takeName").focus();
			$(".tipMsg").text(notice_json.common.job_name);
			return false;
		}else if(takePhone == "" || isPhoneNo(takePhone) == false){
			$("#takePhone").focus();
			$(".tipMsg").text(notice_json.common.applay_phone);
			return false;
		}else if(takeCity == ""){
			$(".tipMsg").text(notice_json.common.city_check);
			return false;
		}else if(takeAddr == ""){
			$(".tipMsg").text(notice_json.common.detail_addr);
			return false;
		}else if($("#takedafult").prop('checked') == false){
			$(".tipMsg").text(notice_json.common.dafult_take);
			return false;
		}
			
	});
	// 会员中心收货地址页面
	function orderVerify(){
		var takeName = $.trim($("#takeName").val());
		var takePhone = $.trim($("#takePhone").val());
		var takeCity = $.trim($("#takeCity").val());
		var takeAddr = $.trim($("#takeAddr").val());
		if(takeName == ""){
			$("#takeName").focus();
			$(".info-tip").text(notice_json.common.job_name);
			return false;
		}else if(takePhone == "" || isPhoneNo(takePhone) == false){
			$("#takePhone").focus();
			$(".info-tip").text(notice_json.common.applay_phone);
			return false;
		}else if(takeCity == ""){
			$(".info-tip").text(notice_json.common.city_check);
			return false;
		}else if(takeAddr == ""){
			$(".info-tip").text(notice_json.common.detail_addr);
			return false;
		}
		$(".info-tip").text("");
		return true;
	}


	//会员中心   开票资料
	$("#invo-file").change(function(){
	  	$(".invo-group .invo-label").html(notice_json.common.apply_file);
	});

	// 会员中心  修改密码
	function newRePwCheck(){
		var oldpw=$.trim($("#oldpwd").val());
		var newpw=$.trim($("#newpwd").val());
		var newrepw=$("#surepwd").val();
		if(oldpw == ""){
			$("#oldpwd").focus();
			$(".tipMsg").text(notice_json.common.oldPwd);
			return false;
		}else if(newpw == ""){
			$("#oldpwd").focus();
			$(".tipMsg").text(notice_json.common.newPwd);
			return false;
		}else if(newrepw == ""){
			$("#oldpwd").focus();
			$(".tipMsg").text(notice_json.common.oldPwd);
			return false;
		}
		if(oldpw==newpw){
			$(".tipMsg").text(notice_json.common.changePwd);
			return false;
		}
		if(newrepw!=newpw){
			$(".tipMsg").text(notice_json.common.changeNewPwd);
			return false;
		}
		return true;
	}










































































