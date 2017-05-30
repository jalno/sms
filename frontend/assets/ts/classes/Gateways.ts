import * as $ from "jquery";
import "jquery-validation";
import "bootstrap";
import "bootstrap-inputmsg";
interface Number{
	number:string;
	status:number;
	primary:boolean;
}
export class Gateways{
	private static form = $('.gateways_form');
	private static $table_numbers = $('.table-numbers', Gateways.form);
	private static showGatewayFields():void{
		$('select[name=gateway]', Gateways.form).change(function(){
			const $val = $(this).val();
			$(`.gatewayfields:not(.gateway-${$val})`,Gateways.form).hide();
			$('.gatewayfields.gateway-'+$val, Gateways.form).show();
		}).trigger('change');
	}
	private static addNumberListener():void{
		const form = $('#number_add_form');
		let submit = function(){
			const number = $('input[name=number]', form).val();
			const status = parseInt($('select[name=status]', form).val());
			const primary = $('input[name=primary]', form).prop('checked');
			let numbers: Number[] = Gateways.$table_numbers.data('numbers');
			if(!numbers){
				numbers = [];
			}
			let found = false;
			for(let i =0;i!=numbers.length && !found;i++){
				if(numbers[i].number == number){
					found = true;
				}
			}
			if(!found){
				if(primary){
					for(let i =0;i!=numbers.length;i++){
						if(numbers[i].primary){
							numbers[i].primary = false;
						}
					}
				}
				let newNumber: Number = {
					number:number,
					status:status,
					primary:primary
				};
				numbers.push(newNumber);
				Gateways.$table_numbers.data('numbers', numbers);
				Gateways.rebuildNumbersTable();
				$('#number-add').modal('hide');
			}else{
				$('input[name=number]', form).inputMsg({
					message: "این شماره قبلا وارد شده"
				});
			}
		}
		form.validate({
			rules: {
				number: {
					required: true
				}
			},
			submitHandler: submit
		});
		$('#number-add').on('hide.bs.modal', function(){
			(form[0] as HTMLFormElement).reset();
		});
	}
	private static rebuildNumbersTable():void{
		let numbers :Number[] = Gateways.$table_numbers.data('numbers');
		if(!numbers){
			numbers = [];
		}
		let html = '';
		for(let i =0;i!=numbers.length;i++){
			let status,primary;
			switch(numbers[i].status){
				case(1):status = 'فعال';break;
				case(2):status = 'غیرفعال';break;
			}
			primary = numbers[i].primary ? '<label class="label label-success">بله</label>' : 'خیر';

			html += '<tr>';
			html += '<td>'+(i+1)+'</td>';
			html += '<td>'+numbers[i].number+'</td>';
			html += '<td>'+status+'</td>';
			html += '<td>'+primary+'</td>';
			html += '<td class="center">';
				html += '<a href="#" class="btn btn-xs btn-danger btn-delete tooltips" data-number="'+numbers[i].number+'" title="حذف"><i class="fa fa-trash"></i></a>';
			html += '</td>';
			html += '</tr>';
		}
		let $tbody = $('tbody', Gateways.$table_numbers);
		$tbody.html(html);
		$('.btn-delete', $tbody).on('click', Gateways.btnNumberDeleteClick);
		html = '';
		for(let i =0;i!=numbers.length;i++){
			for(let x in numbers[i]){
				if(x != 'primary' || numbers[i][x]){
					if(x == 'primary'){
						numbers[i][x] = true;
					}
					html += `<input type="hidden" name="numbers[${i}][${x}]" value="${numbers[i][x]}">`;
				}
			}
		}
		$('.numbersfields', Gateways.form).html(html);
	}
	private static btnNumberDeleteClick(e:Event):void{
		e.preventDefault();
		$('#number_delete_form input[name=number]').val($(this).data('number'));
		$('#number-delete').modal('show');
	}
	private static formNumberDeleteSubmit():void{
		$('#number_delete_form').on('submit', function(e){
			e.preventDefault();
			let number = $('input[name=number]', this).val();
			let numbers : Number[]= Gateways.$table_numbers.data('numbers');
			for(let i =0;i!=numbers.length;i++){
				if(numbers[i].number == number){
					numbers.splice(i, 1);
					break;
				}
			}
			$('#number-delete').modal('hide');
			Gateways.rebuildNumbersTable();
		});
	}
	public static init():void{
		Gateways.showGatewayFields();
		Gateways.addNumberListener();
		Gateways.formNumberDeleteSubmit();
		Gateways.rebuildNumbersTable();
	}
	public static initIfNeeded():void{
		if($('body').hasClass('sms_gateways') && Gateways.form.length > 0){
			Gateways.init();
		}
	}
}