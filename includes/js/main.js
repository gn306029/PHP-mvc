$(function(){
	// 登入
	$("#li_login").click(function(){
		window.location.href = "/MVC/?controller=login&action=index";
	})
	// 登出
	$("#li_logout").click(function(){
		let ajax_logout = new Ajax_Logout("login","logout",$("#form_infor").serialize());
		ajax_logout.Run();
	})
})