<?php
use packages\base\Translator;
use packages\sms\GateWay\Number;
use packages\userpanel;

$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus"></i>
                <span><?php echo t('settings.sms.gateways.add'); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <form class="gateways_form" action="<?php echo userpanel\url('settings/sms/gateways/add'); ?>" method="post">
						<div class="numbersfields"></div>
						<div class="col-md-6">
							<?php
                            $this->createField([
                                'name' => 'title',
                                'label' => t('sms.gateway.title'),
                            ]);
$this->createField([
    'name' => 'gateway',
    'type' => 'select',
    'label' => t('sms.gateway.type'),
    'options' => $this->getGatewaysForSelect(),
]);
$this->createField([
    'name' => 'status',
    'type' => 'select',
    'label' => t('sms.gateway.status'),
    'options' => $this->getGatewayStatusForSelect(),
]);
?>

							<table class="table table-numbers">
								<thead>
									<tr>
										<th>#</th>
										<th><?php echo t('sms.number'); ?></th>
										<th><?php echo t('sms.number.status'); ?></th>
										<th><?php echo t('sms.number.primary'); ?></th>
										<th class="table-tools">
											<a class="btn btn-xs btn-link btn-number-add tooltips" title="<?php echo t('sms.number.add'); ?>" href="#number-add" data-toggle="modal"><i class="fa fa-plus"></i></a>
										</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="col-md-6">
							<?php
foreach ($this->getGateways()->get() as $gateway) {
    $name = $gateway->getName();
    echo "<div class=\"gatewayfields gateway-{$name}\">";
    foreach ($gateway->getFields() as $field) {
        $this->createField($field);
    }
    echo '</div>';
}
?>
						</div>
						<div class="col-md-12">
			                <p>
			                    <a href="<?php echo userpanel\url('settings/sms/gateways'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo t('return'); ?></a>
			                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo t('submit'); ?></button>
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
		<h4 class="modal-title"><?php echo t('sms.number.add'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="number_add_form" class="form-horizontal" action="#" method="POST">
			<?php
            $this->setHorizontalForm('sm-3', 'sm-9');
$feilds = [
    [
        'type' => 'number',
        'name' => 'number',
        'label' => t('sms.number'),
        'ltr' => true,
    ],
    [
        'type' => 'select',
        'name' => 'status',
        'label' => t('sms.number.status'),
        'options' => [
            [
                'value' => Number::active,
                'title' => t('sms.number.status.active'),
            ],
            [
                'value' => Number::deactive,
                'title' => t('sms.number.status.deactive'),
            ],
        ],
    ],
    [
        'type' => 'checkbox',
        'label' => t('sms.number.primary'),
        'name' => 'primary',
        'options' => [
            [
                'value' => 1,
            ],
        ],
    ],
];
foreach ($feilds as $input) {
    echo $this->createField($input);
}
?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="number_add_form" class="btn btn-success"><?php echo t('submit'); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t('cancel'); ?></button>
	</div>
</div>
<div class="modal fade" id="number-delete" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t('sms.number.delete'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="number_delete_form" class="form-horizontal" action="#" method="POST">
			<input type="hidden" name="number" value="">
			<p>آیا شما از حذف این شماره مطمئن هستید؟</p>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="number_delete_form" class="btn btn-danger"><?php echo t('submit'); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
