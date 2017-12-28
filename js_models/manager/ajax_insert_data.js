class Ajax_Insert_Data extends Ajax_Class {

    constructor(model, action,args) {
        super(model, action, args);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        $("#course_content").html("");
        switch(response["Type"]){
            case "Success":
                alert(response["Msg"]);
                break;
            case "Error":
                $("#course_content").append("<span class='error_msg'>"+response["Msg"]+"<span>");
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
