var addGateway = function(){
	var form = $('.create_form');
	var $table_numbers = $('.table-numbers', form);
	var showGatewayFields = function(){
		$('select[name=gateway]', form).change(function(){
			var $val = $(this).val();
			$('.gatewayfields:not(.gateway-'+$val+")",form).hide();
			$('.gatewayfields.gateway-'+$val, form).show();
		}).trigger('change');
	};
	var addNumberListener = function(){
		var number_form = $('#number_add_form');
		var validator = function(){
			number_form.validate({
	            rules: {
	                number: {
	                    required: true
	                }
	            },
	            submitHandler: submit
	        });
		}
		var submit = function(){
			var number = $('input[name=number]', number_form).val();
			var status = parseInt($('select[name=status]', number_form).val());
			var primary = $('input[name=primary]', number_form).prop('checked');
			var numbers = $table_numbers.data('numbers');
			if(!numbers){
				numbers = new Array();
			}
			var found =false;
			for(var i =0;i!=numbers.length && !found;i++){
				if(numbers[i].number == number){
					found = true;
				}
			}
			if(!found){
				if(primary){
					for(var i =0;i!=numbers.length;i++){
						if(numbers[i].primary){
							numbers[i].primary = false;
						}
					}
				}
				numbers.push({
					number:number,
					status:status,
					primary:primary
				});
				$table_numbers.data('numbers', numbers);
				rebuildNumbersTable();

				$('#number-add').modal('hide');
			}else{
				$('input[name=number]', number_form).inputMsg({
					message: "این شماره قبلا وارد شده"
				});
			}
		}
		validator();
		$('#number-add').on('hide.bs.modal', function(){
			number_form[0].reset();
		});
	}
	var rebuildNumbersTable = function(){
		var numbers = $table_numbers.data('numbers');
		if(!numbers){
			numbers = new Array();
		}
		var html = '';
		for(var i =0;i!=numbers.length;i++){
			var status,primary;
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
		var $tbody = $('tbody', $table_numbers);
		$tbody.html(html);
		$('.btn-delete', $tbody).on('click', btnNumberDeleteClick);
		var html = '';
		for(var i =0;i!=numbers.length;i++){
			for(var x in numbers[i]){
				if(x != 'primary' || numbers[i][x]){
					if(x == 'primary'){
						numbers[i][x] = 1;
					}
					html += '<input type="hidden" name="numbers['+i+']['+x+']" value="'+numbers[i][x]+'">';
				}
			}
		}
		$('.numbersfields', form).html(html);

	}
	var btnNumberDeleteClick = function(e){
		e.preventDefault();
		$('#number_delete_form input[name=number]').val($(this).data('number'));
		$('#number-delete').modal('show');
	}
	var formNumberDeleteSubmit = function(){
		$('#number_delete_form').on('submit', function(e){
			e.preventDefault();
			var number = $('input[name=number]', this).val();
			var numbers = $table_numbers.data('numbers');
			for(var i =0;i!=numbers.length;i++){
				if(numbers[i].number == number){
					numbers.splice(i, 1);
					break;
				}
			}
			$('#number-delete').modal('hide');
			rebuildNumbersTable();
		});
	}
	return {
		init: function() {
			showGatewayFields();
			addNumberListener();
			formNumberDeleteSubmit();
			rebuildNumbersTable();
		}
	}
}();
$(function(){
	addGateway.init();
});
