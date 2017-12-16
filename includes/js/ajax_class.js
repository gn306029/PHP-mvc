/*
	統一由這裡傳送到 Ajax Router
*/

class Ajax_Class{

	constructor(controller, js_action,args) {
        this.controller = controller;
        this.php_action = js_action;
        this.args = args;
        this.php = true;
    }

    Run(){
    	let self = this;
    	$.ajax({
    		url:"ajax_routers.php?controller="+this.controller+"&action="+this.php_action,
    		data:this.args,
    		type:"POST",
    		success:function(res){
    			self.Ajax_Success(res);
    		},
    		error:function(jqXHR,exception){
    			self.Ajax_Error(jqXHR.responseText);
    		}
    	})
    }

}