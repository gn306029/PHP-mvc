class Ajax_Show_Student extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        switch (response["Type"]) {
            case "Success":
                // 將學生資料整理成Table並顯示
                $("#course_detail").html("");
                let data = response["Msg"];
                let title = ["學號","姓名","科系","班級","成績"];
                let html = "<form id='student'><table id='vue_infor' >";
                html += "<tr>";
                for(let i in title){
                    html += "<td class='td_content'>"+title[i]+"</td>";
                }
                html += "</tr>";
                html += "<tr><td><input type='hidden' name='course_id' value='"+response["Course_ID"]+"'></td></tr>";
                for(let i in data){
                    html += "<tr>";
                    html += "<td class='td_content'><input type='hidden' name='SID[]' value='"+data[i]["SID"]+"'>"+data[i]["SID"]+"</td>";
                    html += "<td class='td_content'>"+data[i]["Name"]+"</td>";
                    html += "<td class='td_content'>"+data[i]["faculty"]+"</td>";
                    html += "<td class='td_content'>"+data[i]["Grade"]+"</td>";
                    html += "<td class='td_content'><input type='text' name='score[]' value='"+data[i]["Score"]+"'/></td>";
                    html += "</tr>";
                }
                html += "</table><input type='button' id='send_score' onclick=\"(new Ajax_Save_Score('course','save_score',$('#student').serialize())).Run()\" value='Save'></form>";
                $("#course_detail").append(html);
                let script = "<script id=\"save_script\" src=\"js_models/course/ajax_save_score.js\"></script>";
                // 判斷是否加載過了
                if($("#save_script").length == 0){
                    $("head").append(script);
                }
                break;
            case "SearchNotFound":
                $("#course_content").html("<p>"+response["Msg"]+"</p>");
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
