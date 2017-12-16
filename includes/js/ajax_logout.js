class Ajax_Logout extends Ajax_Class{

	constructor(controller,js_action){
		super(controller,js_action,null);
	}

	Ajax_Success(res){
		let response = JSON.parse(res);

		switch(response["Type"]){
			case "Success":
				window.location.href = "/MVC/?controller=default&action=index";
				break;
		}
	}

	Ajax_Error(error){
		console.log(error);
	}

}