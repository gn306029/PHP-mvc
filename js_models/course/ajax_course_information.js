class Ajax_Course_Information extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        
        let response = JSON.parse(res);
        $("#course_content").html("");
        switch(response["Type"]){
            case "Success":
                let data = response["Msg"];
                let html = "<div class='course_detail'>";
                html += "<ul>";
                for(let i=0;i<Object.keys(data["information"][0]).length/2;i++){
                    html += "<li>"+data["information"][0][i]+"</li>";
                }
                html += "</ul>";
                if(!Array.isArray(data["evaluation"])){
                    html += "<span>"+data["evaluation"]+"</span>";
                }else{
                    for(let i=0;i<Object.keys(data["evaluation"]).length;i++){
                        html += "<div class='evaluation'>";
                        html += "<span>課程評價："+data["evaluation"][i]["CE"]+"</span>&nbsp&nbsp";
                        html += "<span>教師評價："+data["evaluation"][i]["TE"]+"</span>";
                        html += "</div>";
                    }
                }
                html += "</div>";
                $("#course_content").append(html);
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
