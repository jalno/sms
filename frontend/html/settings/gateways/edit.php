<?php
use \packages\base;
use \packages\base\Json;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\userpanel\Date;
use \packages\sms\GateWay\Number;
use \themes\clipone\Utility;

$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-edit"></i>
                <span><?php echo Translator::trans("settings.sms.gateways.edit"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <form class="gateways_form" action="<?php echo userpanel\url('settings/sms/gateways/edit/'.$this->getGateway()->id); ?>" method="post">
						<div class="numbersfields"></div>
						<div class="col-md-6">
							<?php
							$this->createField(array(
								'name' => 'title',
								'label' => Translator::trans("sms.gateway.title")
							));
							$this->createField(array(
								'name' => 'gateway',
								'type' => 'select',
								'label' => Translator::trans("sms.gateway.type"),
								'options' => $this->getGatewaysForSelect()
							));
							$this->createField(array(
								'name' => 'status',
								'type' => 'select',
								'label' => Translator::trans("sms.gateway.status"),
								'options' => $this->getGatewayStatusForSelect()
							));
							?>

							<table class="table table-numbers" data-numbers='<?php echo json\encode($this->getNumbersData()); ?>'>
								<thead>
									<tr>
										<th>#</th>
										<th><?php echo Translator::trans('sms.number'); ?></th>
										<th><?php echo Translator::trans('sms.number.status'); ?></th>
										<th><?php echo Translator::trans('sms.number.primary'); ?></th>
										<th class="table-tools">
											<a class="btn btn-xs btn-link btn-number-add tooltips" title="<?php echo Translator::trans('sms.number.add'); ?>" href="#number-add" data-toggle="modal"><i class="fa fa-plus"></i></a>
										</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="col-md-6">
							<?php
							foreach($this->getGateways() as $gateway){
								$name = $gateway->getName();
								echo("<div class=\"gatewayfields gateway-{$name}\">");
								foreach($gateway->getFields() as $field){
									$this->createField($field);
								}
								echo("</div>");
							}
							?>
						</div>
						<div class="col-md-12">
			                <p>
			                    <a href="<?php echo userpanel\url('settings/sms/gateways'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo Translator::trans('return'); ?></a>
			                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo Translator::trans("submit"); ?></button>
			                </p>
						</div>
	                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="number-add" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo Translator::trans('sms.number.add'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="number_add_form" class="form-horizontal" action="#" method="POST">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'type' => 'number',
					'name' => 'number',
					'label' => Translator::trans("sms.number"),
					'ltr' => true
				),
				array(
					'type' => 'select',
					'name' => 'status',
					'label' => Translator::trans("sms.number.status"),
					'options' => array(
						array(
							'value' => Number::active,
							'title' => Translator::trans("sms.number.status.active")
						),
						array(
							'value' => Number::deactive,
							'title' => Translator::trans("sms.number.status.deactive")
						)
					)
				),
				array(
					'type' => 'checkbox',
					'label' => Translator::trans('sms.number.primary'),
					'name' => 'primary',
					'options' => array(
						array(
							'value' => 1
						)
					)
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="number_add_form" class="btn btn-success"><?php echo Translator::trans("submit"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo Translator::trans('cancel'); ?></button>
	</div>
</div>
<div class="modal fade" id="number-delete" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo Translator::trans('sms.number.delete'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="number_delete_form" class="form-horizontal" action="#" method="POST">
			<input type="hidden" name="number" value="">
			<p>آیا شما از حذف این شماره مطمئن هستید؟</p>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="number_delete_form" class="btn btn-danger"><?php echo Translator::trans("submit"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo Translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
