class Ajax_History_Search extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        
        let response = JSON.parse(res);
        $("#course_content").html("");
        switch(response["Type"]){
            case "Success":
                let data = JSON.parse(response["Msg"]);

                if(response["SearchType"] == "Grade"){
                    let html = "<table id='vue_infor' >";
                    html += "<tr>";
                    html += "<td class='td_content' v-for='todo in list'>{{todo.text}}</td>";
                    html += "</tr>";
                    for(let i in data){
                        html += "<tr>";
                        html += "<td class='td_content'>"+data[i]["year"]+"</td>";
                        html += "<td class='td_content'>"+Math.round(data[i]["grade"]*100)/100+"</td>";
                        html += "<td class='td_content'>"+data[i]["rank"]+"</td>";
                        html += "</tr>";
                    }
                    html += "</table>";
                    $("#course_content").append(html);
                    new Vue({
                        el:"#vue_infor",
                        data:{
                            list:[
                                {text:"學年度"},
                                {text:"成績"},
                                {text:"排名"}
                            ]
                        }
                    })
                }else if(response["SearchType"] == "Curr"){
                    let html = "<table id='vue_infor'>";
                    html += "<tr>";
                    html += "<tr>";
                    html += "<td class='td_content' v-for='todo in list'>{{todo.text}}</td>";
                    html += "</tr>";
                    for(let i in data){
                        html += "<tr>";
                        for(let j=0;j<Object.keys(data[i]).length/2;j++){
                            if(j == 0){
                                html += "<td class='td_content'><a href='#' class='course_id' id="+data[i][j]+">"+data[i][j]+"</a></td>";
                            }else{
                                html += "<td class='td_content'>"+data[i][j]+"</td>";
                            }
                            
                        }
                        html += "</tr>";
                    }
                    html += "</table>";
                    $("#course_content").append(html);
                    new Vue({
                        el:"#vue_infor",
                        data:{
                            list:[
                                {text:"課程編號"},
                                {text:"課程名稱"},
                                {text:"學年度"},
                                {text:"授課講師"},
                                {text:"教室"},
                                {text:"類型"},
                                {text:"備註"},
                                {text:"大綱"},
                                {text:"學分數"},
                                {text:"上課時間"}
                            ]
                        }
                    })
                }
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
