class Ajax_Course_List extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        $("#course_content").html("");
        $("#course_detail").html("");
        switch (response["Type"]) {
            case "Success":
                //console.log(response)
                let data = response["Msg"];
                let html = "<table id='vue_infor' >";
                html += "<tr>";
                html += "<td class='td_content' v-for='todo in list'>{{todo.text}}</td>";
                html += "</tr>";
                for(let i in data){
                    html += "<tr>";
                    html += "<td class='td_content'><a href='#' class='course_id' id="+data[i]["Course_ID"]+">"+data[i]["Course_ID"]+"</a></td>";
                    html += "<td class='td_content'>"+data[i]["Name"]+"</td>";
                    html += "<td class='td_content'>"+data[i]["year"]+"</td>";
                    html += "<td class='td_content'>"+data[i]["remarks"]+"</td>";
                    html += "<td class='td_content'>"+data[i]["category"]+"</td>";
                    if(response["Button"] == "Student"){
                        html += "<td class='td_content'><button class='search_student' value='"+data[i]["Course_ID"]+"' style='width:100%'>學生清單</button></a></td>";
                    }else if(response["Button"] == "Score"){
                        html += "<td class='td_content'><button class='search_student' value='"+data[i]["Course_ID"]+"&access=OK' style='width:100%'>學生清單</button></a></td>";
                    }else{
                        html += "<td class='td_content'><button class='set_outline' value='"+data[i]["Course_ID"]+"' style='width:100%'>設定課綱</button></a></td>";
                    }
                    html += "</tr>";
                }
                html += "</table>";
                $("#course_content").append(html);
                let script = "<script id=\"modify_script\" src=\"js_models/course/ajax_show_student.js\"></script>";
                // 判斷是否加載過了
                if($("#modify_script").length == 0){
                    $("head").append(script);
                }
                new Vue({
                    el:"#vue_infor",
                    data:{
                        list:[
                            {text:"課程編號"},
                            {text:"課程名稱"},
                            {text:"學年度"},
                            {text:"備註"},
                            {text:"類別"},
                            {text:""}
                        ]
                    }
                })
                break;
            case "Error":
                $("#course_content").html("<p>"+response["Msg"]+"</p>");
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
