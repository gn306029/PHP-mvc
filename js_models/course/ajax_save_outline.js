class Ajax_Save_Outline extends Ajax_Class {

    constructor(model, action, arg) {
        super(model, action, arg);
    }

    Ajax_Success(res) {
        let response = JSON.parse(res);
        switch (response["Type"]) {
            case "Success":
                alert(response["Msg"]);
                break;
            case "Error":
                alert(response["Msg"]);
                break;
        }
    }

    Ajax_Error(error) {
        console.log(error);
    }

}
