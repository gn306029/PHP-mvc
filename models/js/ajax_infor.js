class Ajax_Infor extends Ajax_Class{

	constructor(controller,js_action){
		super(controller,js_action,null);
	}

	Ajax_Success(res){
		let response = JSON.parse(res);
		//使用動態載入
		switch(response["Type"]){
			case "Success":
				let data = JSON.parse(response["Msg"]);
				let html = "<form>";
				if(typeof data[0]["SID"] != "undefined"){
					html += "<span>學號："+data[0]["SID"]+"</span><br>";
					html += "<span>姓名："+data[0]["Name"]+"</span><br>";
					html += "<span>身分證字號："+data[0]["ID"]+"</span><br>";
					html += "<span>生日：<input type=\"date\" value=\""+data[0]["birth"]+"\" name=\"birth\"/></span><br>";
					if(data[0]["Gender"] === "1"){
						html += "<span>性別：男</span><br>";
					}else if(data[0]["Gender"] === "0"){
						html += "<span>性別：女</span><br>";
					}
					html += "<span>系所："+data[0]["Faculty"]+"</span><br>";
					html += "<span>學制："+data[0]["Academic"]+"</span><br>";
					html += "<span>班級："+data[0]["Grade"]+"</span><br>";
					html += "<span>住址：<input type=\"text\" name=\"address\" value=\""+data[0]["Address"]+"\"/></span><br>";
					html += "<span>手機：<input type=\"text\" name=\"cellphone\" value=\""+data[0]["Cellphone"]+"\"/></span><br>";
					html += "<span>電話：<input type=\"text\" name=\"phone\" value=\""+data[0]["Phone"]+"\"/></span><br>";
					html += "<span>父親：<input type=\"text\" name=\"father\" value=\""+data[0]["Father"]+"\"/></span><br>";
					html += "<span>父親手機：<input type=\"text\" name=\"father_phone\" value=\""+data[0]["Father_Phone"]+"\"/></span><br>";
					html += "<span>母親：<input type=\"text\" name=\"mather\" value=\""+data[0]["Mather"]+"\"/></span><br>";
					html += "<span>母親手機：<input type=\"text\" name=\"mather_phone\" value=\""+data[0]["Mather_Phone"]+"\"/></span><br>";
					html += "<span>緊急聯絡人：<input type=\"text\" name=\"urgent_man\" value=\""+data[0]["Urgent_Man"]+"\"/></span><br>";
					html += "<span>緊急聯絡人手機：<input type=\"text\" name=\"urgent_phone\" value=\""+data[0]["Urgent_Phone"]+"\"/></span><br>";
					html += "<span>畢業高中："+data[0]["Highschool"]+"</span><br>";
					html += "<span>在學情況："+data[0]["Status"]+"</span><br>";
				}else{
					//教職員畫面
				}
				html += "</form>";
				$("#manger_content").append(html);
				

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