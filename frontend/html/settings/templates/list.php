<?php
use packages\base\Translator;
use packages\sms\Template;
use packages\userpanel;
use themes\clipone\Utility;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-file-text-o"></i> <?php echo Translator::trans('settings.sms.templates'); ?>
				<div class="panel-tools">
					<?php if ($this->canAdd) { ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo Translator::trans('add'); ?>" href="<?php echo userpanel\url('settings/sms/templates/add'); ?>"><i class="fa fa-plus"></i></a>
					<?php } ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo Translator::trans('search'); ?>" href="#search" data-toggle="modal"><i class="fa fa-search"></i></a>
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
								<th><?php echo Translator::trans('sms.template.name'); ?></th>
								<th><?php echo Translator::trans('sms.template.lang'); ?></th>
								<th><?php echo Translator::trans('sms.template.status'); ?></th>
								<?php if ($hasButtons) { ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php
    foreach ($this->getDataList() as $item) {
        $this->setButtonParam('view', 'link', userpanel\url('settings/sms/templates/view/'.$item->id));
        $this->setButtonParam('edit', 'link', userpanel\url('settings/sms/templates/edit/'.$item->id));
        $this->setButtonParam('delete', 'link', userpanel\url('settings/sms/templates/delete/'.$item->id));
        $statusClass = Utility::switchcase($item->status, [
            'label label-success' => Template::active,
            'label label-danger' => Template::deactive,
        ]);
        $statusTxt = Utility::switchcase($item->status, [
            'sms.template.status.active' => Template::active,
            'sms.template.status.deactive' => Template::deactive,
        ]);
        $name = Translator::trans('sms.template.name.'.$item->name);
        ?>
						<tr>
							<td class="center"><?php echo $item->id; ?></td>
							<td><?php echo $name ? $name : $item->name; ?></td>
							<td><?php echo Translator::trans('translations.langs.'.$item->lang); ?></td>
							<td><span class="<?php echo $statusClass; ?>"><?php echo Translator::trans($statusTxt); ?></span></td>
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
		<h4 class="modal-title"><?php echo Translator::trans('search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="templates_search_form" class="form-horizontal" action="<?php echo userpanel\url('settings/sms/templates'); ?>" method="GET">
			<?php
            $this->setHorizontalForm('sm-3', 'sm-9');
$feilds = [
    [
        'name' => 'id',
        'type' => 'number',
        'label' => Translator::trans('sms.template.id'),
    ],
    [
        'name' => 'name',
        'label' => Translator::trans('sms.template.name'),
    ],
    [
        'type' => 'select',
        'name' => 'status',
        'label' => Translator::trans('sms.template.status'),
        'options' => $this->getTemplateStatusForSelect(),
    ],
    [
        'type' => 'select',
        'label' => Translator::trans('search.comparison'),
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
		<button type="submit" form="templates_search_form" class="btn btn-success"><?php echo Translator::trans('search'); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo Translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
