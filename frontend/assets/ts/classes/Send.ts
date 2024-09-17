import "jquery-ui/ui/widgets/autocomplete.js";
import "jquery-validation";
import {Router, webuilder} from "webuilder";
import "webuilder"
interface user{
	id:number;
	name:string;
	lastname:string;
	email:string;
	cellphone:string;
}
interface searchResponse extends webuilder.AjaxResponse{
	items: user[];
}
export default class Send{
	private static form = $('#send_sms');
	private static runUserListener = function(){
		function select(event, ui):boolean{
			$(this).val(ui.item.cellphone);
			return false;
		}
		$("input[name=to]", Send.form).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: Router.url("userpanel/users"),
					dataType: "json",
					data: {
						ajax: 1,
						word: request.term
					},
					success: function( data: searchResponse) {
						if(data.status){
							response( data.items );
						}
					}
				});
			},
			select: select,
			focus: select,
			create: function() {
		        $(this).data('ui-autocomplete')._renderItem = function( ul, item ) {
					return $( "<li>" )
						.append( "<strong>" + item.name+(item.lastname ? ' '+item.lastname : '')+ "</strong><small class=\"ltr\">"+item.email+"</small><small class=\"ltr\">"+item.cellphone+"</small>" )
						.appendTo( ul );
				}
			}
		});
	}
	private static setValidator():void{
		Send.form.validate({
			rules: {
				from: {
					required: true
				},
				to: {
					required: true,
					number:true
				},
				text:{
					required:true
				}
			},
			submitHandler: (form) => {
				$(form).formAjax({
					success:() => {
						$.growl.notice({
							title:"ارسال شد",
							message:"پیامک با موفقیت ارسال شد"
						});
						($(form)[0] as HTMLFormElement).reset();
					},
					error: function(error:webuilder.AjaxError){
						if(error.error == 'data_validation'){
							let $input = $('[name='+error.input+']');
							let $params = {
								title: 'خطا',
								message: 'داده وارد شده معتبر نیست'
							};
							if($input.length){
								$input.inputMsg($params);
							}else{
								$.growl.error($params);
							}
						}else{
							$.growl.error({
								title:"خطا",
								message:'درخواست شما توسط سرور قبول نشد'
							});
						}
					}
				});
			}
		});
	}
	public static init():void{
		Send.setValidator();
		Send.runUserListener();
	}
	public static initIfNeeded():void{
		if($('body').hasClass('smsSend')){
			Send.init();
		}
	}
}
