<?php
use packages\base\Translator;
use packages\sms\GateWay;
use packages\userpanel;
use themes\clipone\Utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-rss"></i> <?php echo t('settings.sms.gateways'); ?>
				<div class="panel-tools">
					<?php if ($this->canAdd) { ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo t('add'); ?>" href="<?php echo userpanel\url('settings/sms/gateways/add'); ?>"><i class="fa fa-plus"></i></a>
					<?php } ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo t('search'); ?>" href="#search" data-toggle="modal"><i class="fa fa-search"></i></a>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
                        $hasButtons = $this->hasButtons();
?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo t('sms.gateway.title'); ?></th>
								<th><?php echo t('sms.gateway.type'); ?></th>
								<th><?php echo t('sms.gateway.status'); ?></th>
								<?php if ($hasButtons) { ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php
    foreach ($this->getDataList() as $item) {
        $gateway = $this->getGateways()->getByHandler($item->handler);
        if ($gateway) {
            $gateway = $gateway->getName();
        }
        $this->setButtonParam('view', 'link', userpanel\url('settings/sms/gateways/view/'.$item->id));
        $this->setButtonParam('edit', 'link', userpanel\url('settings/sms/gateways/edit/'.$item->id));
        $this->setButtonParam('delete', 'link', userpanel\url('settings/sms/gateways/delete/'.$item->id));
        $statusClass = Utility::switchcase($item->status, [
            'label label-success' => GateWay::active,
            'label label-danger' => GateWay::deactive,
        ]);
        $statusTxt = Utility::switchcase($item->status, [
            'sms.gateway.status.active' => GateWay::active,
            'sms.gateway.status.deactive' => GateWay::deactive,
        ]);
        ?>
						<tr>
							<td class="center"><?php echo $item->id; ?></td>
							<td><?php echo $item->title; ?></td>
							<td><?php echo t('sms.gateway.'.$gateway) ? t('sms.gateway.'.$gateway) : $gateway; ?></td>
							<td><span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span></td>
							<?php
            if ($hasButtons) {
                echo '<td class="center">'.$this->genButtons().'</td>';
            }
        ?>
						</tr>
						<?php
    }
?>
						</tbody>
					</table>
				</div>
				<?php $this->paginator(); ?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t('search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="gateways_search_form" class="form-horizontal" action="<?php echo userpanel\url('settings/sms/gateways'); ?>" method="GET">
			<?php
            $this->setHorizontalForm('sm-3', 'sm-9');
$feilds = [
    [
        'name' => 'id',
        'type' => 'number',
        'label' => t('ticket.id'),
    ],
    [
        'name' => 'title',
        'label' => t('department.title'),
    ],
    [
        'type' => 'select',
        'label' => t('search.comparison'),
        'name' => 'comparison',
        'options' => $this->getComparisonsForSelect(),
    ],
];
foreach ($feilds as $input) {
    echo $this->createField($input);
}
?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="gateways_search_form" class="btn btn-success"><?php echo t('search'); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
