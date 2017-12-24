class Ajax_Now_Course extends Ajax_Class {

    constructor(model, action) {
        super(model, action, null);
    }

    Ajax_Success(res) {
        
        let response = JSON.parse(res);
        $("#course_content").html("");
        switch(response["Type"]){
            case "Success":
                let data = JSON.parse(response["Msg"]);
                console.log(data)

                let html = "<table id='vue_infor'>";
                html += "<tr>";
                html += "<td class='td_content' v-for='todo in list'>{{todo.text}}</td>";
                html += "</tr>";
                for(let i=0;i<Object.keys(data).length;i++){
                    html += "<tr>";
                    for(let j=0;j<Object.keys(data[i]).length/2;j++){
                        html += "<td class='td_content'>"+data[i][j]+"</td>";
                    }
                    html += "<td class='td_content'><button type='button' id='show_evol' value='"+data[i][0]+"' style='width:100%;'>填寫評價</td>";
                    html += "</tr>";
                }
                html += "</table>";
                $("#course_content").append(html);
                let script = "<script id=\"modify_script\" src=\"js_models/course/ajax_course_evol.js\"></script>";
                // 判斷是否加載過了
                if($("#modify_script").length == 0){
                    $("head").append(script);
                }
                break;
            case "Error":
                $("#course_content").append("<span class='error_msg'>"+response["Msg"]+"<span>");
                break;
        }
        new Vue({
            el:"#vue_infor",
            data:{
                list:[
                    {text:"課程編號"},
                    {text:"課程名稱"},
                    {text:"\t"}
                ]
            }
        })
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
