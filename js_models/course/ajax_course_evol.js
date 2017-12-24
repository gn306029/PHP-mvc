class Ajax_Course_Evol extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        $("#course_content").html("");
        switch(response["Type"]){
            case "Success_Show":
                let data = JSON.parse(response["Msg"]);
                let html = "<div>";
                html += "<form id='evol_infor'><span>"+data["Course_ID"]+"</span><br>";
                html += "<span>"+data["Course"]+"</span><br>";
                html += "課程評價：<textarea name='Hello' maxlength='250'/><br>";
                html += "講師評價：<textarea name='Hello' maxlength='250'/><br>";
                html += "<input type='button' value='Send'/>";
                html += "</div>";
                $("#course_content").append(html);
                break;
            case "IDError":
                $("#course_content").append("<span class='error_msg'>"+response["Msg"]+"<span>");
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
