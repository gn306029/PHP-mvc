class Ajax_Save_Score extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        switch (response["Type"]) {
            case "Success":
                alert("成功更新："+response["Success_Update_Count"]+" 筆資料\n錯誤："+response["Error_Count"]+" 筆資料\n無效更新："+response["Invaild_Update_Count"]+" 筆資料");
                break;
            case "Error":
                alert("成功更新："+response["Success_Update_Count"]+" 筆資料\n錯誤："+response["Error_Count"]+" 筆資料\n無效更新："+response["Invaild_Update_Count"]+" 筆資料");
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
