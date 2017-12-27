class Ajax_Course_Evol extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        $("#course_content").html("");
        switch(response["Type"]){
            case "Success_Show":
                let data = response["Msg"][0];
                let html = "<div>";
                html += "<form id='evol_infor'><span>"+data["Course_ID"]+"</span><br>";
                html += "<span>"+data["course"]+"</span><br>";
                html += "課程評價：<textarea name='course' maxlength='250'/><br>";
                html += "講師評價：<textarea name='teacher' maxlength='250'/><br>";
                html += "<input type='hidden' name='course_id' value="+data["Course_ID"]+">";
                html += "<input type='button' id='send_evol' value='Send'/>";
                html += "</div>";
                $("#course_content").append(html);
                break;
            case "Success_Send":
                alert(response["Msg"]);
                break;
            case "InsertError":
                alert(response["Msg"]);
                break;
            case "IDError":
                alert(response["Msg"]);
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
