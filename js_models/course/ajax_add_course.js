class Ajax_Add_Course extends Ajax_Class{

	constructor(controller,js_action,args){
		super(controller,js_action,args);
	}

	Ajax_Success(res){
		let response = JSON.parse(res);
		$("#manger_content").html("");
		$("#manger_detail").html("");
		switch(response["Type"]){
			case "Success":
				let html = "";
				let teacher;
				let category;
				let classroom;
				let remarks;
				let data;
				switch(response["Insert_Type"]){
					case "show_insert":
						teacher = response["teacher"];
						category = response["category"];
						classroom = response["classroom"];
						remarks = response["remarks"];
						html += "<form id='insert_form'>";
						html += "<span class='label'>課程編號：</span><input type='text' name='Course_ID' placeholder='課程編號' maxlength='5' required/><br>";
						html += "<span class='label'>課程名稱：</span><input type='text' name='Name' placeholder='課程名稱' maxlength='20' required/><br>";
						html += "<span class='label'>學年度："+response["year"]+"</span><br>";
						html += "<span class='label'>教師：</span><select name='Teacher_ID'>";
						for(let i=0;i<Object.keys(teacher).length;i++){
							html += "<option value='"+teacher[i]["Teacher_ID"]+"'>"+teacher[i]["Name"]+"</option>";
						}
						html += "</select></br>";
						html += "<span class='label'>類別：</span><select name='Category_ID'>";
						for(let i=0;i<Object.keys(category).length;i++){
							html += "<option value='"+category[i]["Category_ID"]+"'>"+category[i]["Name"]+"</option>";
						}
						html += "</select></br>";
						html += "<span class='label'>教室：</span><select name='Classroom_ID'>";
						for(let i=0;i<Object.keys(classroom).length;i++){
							html += "<option value='"+classroom[i]["Classroom_ID"]+"'>"+classroom[i]["Name"]+"</option>";
						}
						html += "</select></br>";
						html += "<span class='label'>備註：</span><select name='Remarks_ID'>";
						for(let i=0;i<Object.keys(remarks).length;i++){
							html += "<option value='"+remarks[i]["Remarks_ID"]+"'>"+remarks[i]["Name"]+"</option>";
						}
						html += "</select></br>";
						html += "<span class='label'>課程大綱：</span><textarea name='Outline' maxlength='200'></textarea><br>";
						html += "<span class='label'>學分數：</span><input type='text' name='Course_Credit' placeholder='學分數' maxlength='1' required/><br>";
						html += "<span class='label'>上課日：</span><input type='text' name='Day' placeholder='上課日' maxlength='10' required/><br>";
						html += "<span class='label'>上課時間：</span><input type='text' name='Time' placeholder='上課時間 EX:11:00~12:00' maxlength='11' required/><br>";
						html += "</form>"
						html += "<input type='button' onclick=\"(new Ajax_Add_Course('course','insert',$('#insert_form').serialize())).Run()\" value='Save'/>"
						$("#manger_detail").append(html);
						break;
					case "show_modify":
						let title = ["課程編號", "課程名稱", "開課年度", "授課教師", "上課地點",
		                    "類型", "備註", "課程大綱", "學分數", "上課日", "上課時間",""
		                ];
		                html = "<table>";
		                html += "<tr>";
		                for (let i in title) {
		                    html += "<td class=\"td_content\">" + title[i] + "</td>";
		                }
		                html += "</tr>"
		                data = response["Msg"];
		                for (let key in data) {
		                    for (let i=0;i<Object.keys(data[key]).length/2;i++) {
		                        if (i == 0) {
		                            html += "<td class=\"td_content\"><a href='#' class='course_id' id="+data[key][i]+">" + data[key][i] + "</a></td>";
		                        } else if(i == Math.floor(Object.keys(data[key]).length/2)-1){
		                        	html += "<td class=\"td_content\"><button value='"+data[key][0]+"' onclick=\"(new Ajax_Add_Course('course','show_modify_detail','course_id='+$(this).val())).Run()\" style='width:100%;'>修改</button></td>";
		                        } else {
		                            html += "<td class=\"td_content\">" + data[key][i] + "</td>";
		                        }
		                    }
		                    html += "</tr>";
		                }
		                html += "</table>";
		                $("#manger_detail").append(html);
						break;
					case "show_modify_detail":
						data = response["Msg"];
						teacher = response["teacher"];
						category = response["category"];
						classroom = response["classroom"];
						remarks = response["remarks"];
						html = "";
						html += "<form id='modify_form'>";
						html += "<span class='label'>課程編號：</span>"+data[0]["Course_ID"]+"<br>";
						html += "<span class='label'>課程名稱：</span><input type='text' name='Name' placeholder='課程名稱' maxlength='20' value='"+data[0]["Cs_Name"]+"' required/><br>";
						html += "<span class='label'>學年度："+data[0]["Year_Sem"]+"</span><br>";
						html += "<span class='label'>教師：</span><select name='Teacher_ID'>";
						for(let i=0;i<Object.keys(teacher).length;i++){
							if(teacher[i]["Teacher_ID"] == data[0]["Teacher_ID"]){
								html += "<option value='"+teacher[i]["Teacher_ID"]+"' selected>"+teacher[i]["Name"]+"</option>";
							}else{
								html += "<option value='"+teacher[i]["Teacher_ID"]+"'>"+teacher[i]["Name"]+"</option>";
							}
						}
						html += "</select></br>";
						html += "<span class='label'>類別：</span><select name='Category_ID'>";
						for(let i=0;i<Object.keys(category).length;i++){
							if(category[i]["Category_ID"] == data[0]["Category_ID"]){
								html += "<option value='"+category[i]["Category_ID"]+"' selected>"+category[i]["Name"]+"</option>";
							}else{
								html += "<option value='"+category[i]["Category_ID"]+"'>"+category[i]["Name"]+"</option>";
							}
						}
						html += "</select></br>";
						html += "<span class='label'>教室：</span><select name='Classroom_ID'>";
						for(let i=0;i<Object.keys(classroom).length;i++){
							if(classroom[i]["Classroom_ID"] == data[0]["Classroom_ID"]){
								html += "<option value='"+classroom[i]["Classroom_ID"]+"' selected>"+classroom[i]["Name"]+"</option>";
							}else{
								html += "<option value='"+classroom[i]["Classroom_ID"]+"'>"+classroom[i]["Name"]+"</option>";
							}
						}
						html += "</select></br>";
						html += "<span class='label'>備註：</span><select name='Remarks_ID'>";
						for(let i=0;i<Object.keys(remarks).length;i++){
							if(remarks[i]["Remarks_ID"] == data[0]["Remarks_ID"]){
								html += "<option value='"+remarks[i]["Remarks_ID"]+"' selected>"+remarks[i]["Name"]+"</option>";
							}else{
								html += "<option value='"+remarks[i]["Remarks_ID"]+"'>"+remarks[i]["Name"]+"</option>";
							}
						}
						html += "</select></br>";
						html += "<input type='hidden' name='Course_ID' value='"+data[0]["Course_ID"]+"'/>"
						html += "<span class='label'>課程大綱：</span><textarea name='Outline' maxlength='200'>"+data[0]["Outline"]+"</textarea><br>";
						html += "<span class='label'>學分數：</span><input type='text' name='Course_Credit' value='"+data[0]["Course_Credit"]+"' placeholder='學分數' maxlength='1' required/><br>";
						html += "<span class='label'>上課日：</span><input type='text' name='Day' placeholder='上課日' value='"+data[0]["Day"]+"' maxlength='10' required/><br>";
						html += "<span class='label'>上課時間：</span><input type='text' name='Time' value='"+data[0]["Time"]+"' placeholder='上課時間 EX:11:00~12:00' maxlength='11' required/><br>";
						html += "</form>"
						html += "<input type='button' onclick=\"(new Ajax_Add_Course('course','modify',$('#modify_form').serialize())).Run()\" value='Save'/>";

						$("#manger_detail").append(html);
						break;
					case "modify":
					case "insert":
						alert(response["Msg"]);
						break;
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