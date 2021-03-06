class Ajax_Forget extends Ajax_Class{

	constructor(controller,js_action,args){
		super(controller,js_action,args);
	}

	Ajax_Success(res){
		let response = JSON.parse(res);

		switch(response["Type"]){
			case "Success":
				alert(response["Msg"]);
				break;
			case "TypeError":
				alert(response["Msg"]);
				break;
			case "UpdateError":
				alert(response["Msg"]);
				break;
		}
	}

	Ajax_Error(error){
		console.log(error);
	}

}