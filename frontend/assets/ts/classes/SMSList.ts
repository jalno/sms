import "jquery-ui/dist/jquery-ui.js";
import {Router, webuilder} from "webuilder";

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
export class SMSList{
	private static form = $('#smslist_search');
	private static runUserListener = function(){
		function select(event, ui):boolean{
			let name = $(this).attr('name');
			name = name.substr(0, name.length - 5);
			$(this).val(ui.item.name+(ui.item.lastname ? ' '+ui.item.lastname : ''));
			$(`input[name="${name}"]`, SMSList.form).val(ui.item.id).trigger('change');
			return false;
		}
		function unselect(){
			if($(this).val() == ""){
				let name = $(this).attr('name');
				name = name.substr(0, name.length - 5);
				$('input[name='+name+']', SMSList.form).val("");
			}
		}
		$("input[name=sender_user_name], input[name=receiver_user_name]", SMSList.form).autocomplete({
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
			change:unselect,
			close:unselect,
			create: function() {
		        $(this).data('ui-autocomplete')._renderItem = function( ul, item ) {
					return $( "<li>" )
						.append( "<strong>" + item.name+(item.lastname ? ' '+item.lastname : '')+ "</strong><small class=\"ltr\">"+item.email+"</small><small class=\"ltr\">"+item.cellphone+"</small>" )
						.appendTo( ul );
				}
			}
		});
	}
	public static init():void{
		SMSList.runUserListener();
	}
	public static initIfNeeded():void{
		if($('body').hasClass('smslist')){
			SMSList.init();
		}
	}
}
