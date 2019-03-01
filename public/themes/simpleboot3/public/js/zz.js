function msg(text,type=0){
	switch(type){
	case 1:
		$('body').append(text);
		break;
	case 0:
		alert(text);
		break;
	}
}