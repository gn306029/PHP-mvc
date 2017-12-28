class Ajax_Insert extends Ajax_Class{

	constructor(controller,js_action,args){
		super(controller,js_action,args);
	}

	Ajax_Success(res){
		let response = JSON.parse(res);
		$("#manger_content").html("");
		switch(response["Type"]){
			case "Success":
				let faculty_data = response["Faculty"];
				let data1_data = response["Data1"];
				let faculty_html = "<span class='label'>科系</span><select name='faculty'>";
				let data1_html = "";
				let separately = "";
				let ID = "";
				switch(response["Insert_Type"]){
					case "student":
						data1_html = "<span class='label'>學制：</span><select name='data1'>";
						for(let i=0;i<Object.keys(data1_data).length;i++){
							data1_html += "<option value='"+data1_data[i]["Academic_ID"]+"'>"+data1_data[i]["academic"]+"</option>";
						}
						ID = "<span class='label'>學號：</span><input type='text' name='SID' placeholder='學號' required/><br>";
						data1_html += "</select><br>";
						separately += "<span class='label'>班級：</span><input type='text' name='Grade' placeholder='班級' required/><br>";
						separately += "<span class='label'>父親：</span><input type='text' name='Father' placeholder='父親'/><br>";
						separately += "<span class='label'>父親電話：</span><input type='text' name='Father_Phone' placeholder='父親電話'/><br>";
						separately += "<span class='label'>母親：</span><input type='text' name='Mather' placeholder='母親'/><br>";
						separately += "<span class='label'>母親電話：</span><input type='text' name='Mather_Phone' placeholder='母親電話'/><br>";
						separately += "<span class='label'>畢業高中：</span><input type='text' name='Highschool' placeholder='畢業高中' required/><br>";
						separately += "<input type='hidden' name='insert_type' value='student'/>";
						separately += "<span class='label'>在學狀態：</span>";
						separately += "<select name='status'>";
						let tmp = ["在學","休學","退學","畢業"];
						for(let i in tmp){
							separately += "<option value='"+tmp[i]+"'>"+tmp[i]+"</option>";
						}
						separately += "</select><br>";
						break;
					case "teacher":
						data1_html = "<span class='label'>工作職稱：</span><select name='data1'>";
						for(let i=0;i<Object.keys(data1_data).length;i++){
							data1_html += "<option value='"+data1_data[i]["Job_ID"]+"'>"+data1_data[i]["job_title"]+"</option>";
						}
						data1_html += "</select><br>";
						ID = "<span class='label'>教職員編號：</span><input type='text' name='Teacher_ID' placeholder='教職員編號' required/><br>";
						separately += "<span class='label'>薪水：</span><input type='text' name='Salary' placeholder='薪水' required/><br>";
						separately += "<input type='hidden' name='insert_type' value='teacher'/>";
						break;
				}
				for(let i=0;i<Object.keys(faculty_data).length;i++){
					faculty_html += "<option value='"+faculty_data[i]["Faculty_ID"]+"'>"+faculty_data[i]["faculty"]+"</option>";
				}
				faculty_html += "</select><br>";
				let html = "<form id='insert_information'>";
				html += ID;
				html += "<span class='label'>姓名：</span><input type='text' name='Name' placeholder='姓名' required/><br>";
				html += "<span class='label'>身分證：</span><input type='text' name='ID' placeholder='身分證' required/><br>";
				html += "<span class='label'>性別：</span><input type='radio' name='Gender' value='1'>男</input><input type='radio' name='Gender' value='0'>女</input><br>";
				html += "<span class='label'>生日：</span><input type='date' name='Birth'/><br>";
				html += "<span class='label'>住址：</span><input type='text' name='Address' placeholder='地址' required/><br>";
				html += "<span class='label'>電話：</span><input type='text' name='Phone' placeholder='電話'/><br>";
				html += "<span class='label'>手機：</span><input type='text' name='Cellphone' placeholder='手機'/><br>";
				html += "<span class='label'>緊急聯絡人：</span><input type='text' name='Urgent_Man' placeholder='姓名' required/><br>";
				html += "<span class='label'>緊急聯絡人電話：</span><input type='text' name='Urgent_Phone' placeholder='姓名' required/><br>";
				html += separately;
				html += faculty_html;
				html += data1_html;
				html += "<span class='label'>密碼：</span><input type='text' name='Password' placeholder='密碼' required/>";
				html += "</form>";
				html += "<input type='button' onclick=\"(new Ajax_Insert_Data('manager','insert',$('#insert_information').serialize())).Run()\" value='Insert' />";
				$("#manger_content").append(html);
				let script = "<script id=\"insert_script\" src=\"js_models/manager/ajax_insert_data.js\"></script>";
                // 判斷是否加載過了
                if($("#insert_script").length == 0){
                    $("head").append(script);
                }
				break;
			case "Error":
				alert(response["Msg"]);
				break;
		}
	}

	Ajax_Error(error){
		console.log(error);
	}

}