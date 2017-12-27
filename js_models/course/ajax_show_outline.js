class Ajax_Show_Outline extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        $("#course_detail").html("");
        switch (response["Type"]) {
            case "Success":
                let data = JSON.parse(response["Msg"]);
                let html = "<div class='course_infor'><form id='course_outline_form'>";
                html += "<span>"+data[0]["Course_ID"]+"</span>";
                html += "<span>"+data[0]["Name"]+"</span>";
                html += "<span>"+data[0]["Course_ID"]+"</span><hr>";
                html += "<span><textarea name='outline'>"+data[0]["Outline"]+"</textarea></span>";
                html += "<input type='hidden' name='course_id' value='"+data[0]["Course_ID"]+"'/>";
                html += "</form>";
                html += "<input type='button' onclick=\"(new Ajax_Save_Outline('course','save_outline',$('#course_outline_form').serialize())).Run()\" value='Save'/>";
                $("#course_detail").append(html);
                let script = "<script id=\"outline_script\" src=\"js_models/course/ajax_save_outline.js\"></script>";
                // 判斷是否加載過了
                if($("#outline_script").length == 0){
                    $("head").append(script);
                }
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
