//文件上传
function file_change($file_file,$file_input,url_file,$span=null){
	$file_file.change(function(e){
		 
		var file = e.target.files[0]; 
		var reader = new FileReader(); 
		if (file) {
			reader.readAsDataURL(file); 
		} else { 
			return false;
		}
		//组装表单数据
			var fordata=new FormData();
			fordata.append("file",file); 
			//ajax
			$.ajax({
				"url":url_file,
				"type":"POST",
				"processData":false,
				"contentType":false,
				"data":fordata,
				"dataType":"json", 
				"success":function(data){
					console.log(data);
					if(data.code==1){
						//上传成功后要给隐藏域赋值
						$file_input.val(data.data); 
					}else{
						$file_input.val('');
					}  
					if($span){
						$span.html(notice_json.common.apply_file);
					}
					
				},
			 	"error": function(event, XMLHttpRequest, ajaxOptions, thrownError){ 
		        	msg(event.responseText,1); 
		        }
			});
	});
}
 //消息提示，关闭调试后把1统一弹出服务器错误
function msg(text,type=0){
	switch(type){
	case 1:
		$('body').append(text);
		alert('data_error');
		break;
	case 0:
		alert(text);
		break;
	}
}
//获取城市信息
function get_citys($select,fid=1,id=0){
	var options='<option value="0">请选择地区</option>';
	if(fid==0){
		$select.html(options); 
		return false;
	}
 
	 $.ajax({
	        type: 'POST',
	        url: city_url,
	        dataType: 'json',
	        data: {'fid':fid},
	        success: function(data){ 
	        	if(data.code!=1){ 
					msg(data.msg);
				}
	        	var list=data.data;
	    		
	    		for(var i in list){
	    			options+='<option value="'+i+'">'+list[i]+'</option>';
	    		} 
	    		$select.html(options); 
	    		if(id>0){
	    			$select.val(id);
	    		}
	        },
	        error: function(event, XMLHttpRequest, ajaxOptions, thrownError){ 
	        	msg(event.responseText,1); 
	        } 
	    }); 
}

//城市选择js初始化
function city_js($country,country,$province,province,$city=null,city=0,$area=null,area=0){
	if($country){
		get_citys($country,-1,country); 
		$country.change(function(){
			country=$(this).val();
			get_citys($province,country,0);
			if($city){
				get_citys($city,0,0); 
			} 
			if($area){
				 get_citys($area,0,0); 
			}
		});
	} 
	 
	if(country>0){
		get_citys($province,country,province);
	} 
	if(province>0 && $city){
		get_citys($city,province,city);
	}
	if($area && city>0){
		get_citys($area,city,area); 
	}
	 
	if($city){
		$province.change(function(){
			province=$(this).val();
			get_citys($city,province,0);
			if($area){
				 get_citys($area,0,0); 
			}
		});
		if($area){
			$city.change(function(){  
				city=$(this).val();
				get_citys($area,city,0); 
				 
			});
		}
		
	}
	
}
 