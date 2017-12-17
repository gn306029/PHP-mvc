class Ajax_Login extends Ajax_Class{

	constructor(controller,js_action,args){
		super(controller,js_action,args);
	}

	Ajax_Success(res){
		let response = JSON.parse(res);

		switch(response["Type"]){
			case "Success":
				window.location.href = "/MVC/";
				break;
			case "InforError":
				alert("帳號或密碼錯誤");
				break;
			case "FormError":
				alert("帳號格式錯誤錯誤");
				break;
		}
	}

	Ajax_Error(error){
		console.log(error);
	}

}