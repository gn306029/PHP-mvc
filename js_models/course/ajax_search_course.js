class Ajax_Search_Course extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        switch (response["Type"]) {
            case "Success":
                let title = ["課程編號", "課程名稱", "開課年度", "授課教師", "上課地點",
                    "類型", "備註", "課程大綱", "學分數", "上課日", "上課時間",
                    "修課狀態", "學期成績"
                ];
                let html = "<table>";
                html += "<tr>";
                for (let i in title) {
                    html += "<td>" + title[i] + "</td>";
                }
                html += "</tr>"
                let data = JSON.parse(response["Msg"]);
                for (let key in data) {

                	html += "<tr>";
                    for (let i=0;i<Object.keys(data[key]).length/2;i++) {
                        if (i == 0) {
                            html += "<td class=\"td_content\"><a href=\"#\">" + data[key][i] + "</a></td>";
                        } else {
                            html += "<td class=\"td_content\">" + data[key][i] + "</td>";
                        }
                    }
                    html += "</tr>";
                }
                html += "</table>";
                $("#course_content").html(html);
                break;
            case "SearchError":
                $("#course_content").html("<p>未搜尋到資料</p>");
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
