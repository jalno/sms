<?php
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Date;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-envelope"></i> <?php echo Translator::trans('sms.get'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo Translator::trans('search'); ?>" href="#search" data-toggle="modal"><i class="fa fa-search"></i></a>
					<?php if ($this->canSend) { ?><a class="btn btn-xs btn-link tooltips" title="<?php echo Translator::trans('sms.send'); ?>" href="<?php echo userpanel\url('sms/send'); ?>"><i class="fa fa-plus"></i></a><?php } ?>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo Translator::trans('sms.receive_at'); ?></th>
								<th><?php echo Translator::trans('sms.user.sender'); ?></th>
								<th><?php echo Translator::trans('sms.number.sender'); ?></th>
								<th><?php echo Translator::trans('sms.number.receiver'); ?></th>
								<th><?php echo Translator::trans('sms.text'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
                            foreach ($this->getDataList() as $row) {
                                ?>
							<tr>
								<td class="center"><?php echo $row->id; ?></td>
								<td class="ltr"><?php echo Date::format('Y/m/d H:i', $row->receive_at); ?></td>
								<td><?php if ($row->sender_user) { ?>
									<a href="<?php echo userpanel\url('users/view/'.$row->sender_user->id); ?>"><?php echo $row->sender_user->name.' '.$row->sender_user->lastname; ?></a>
									<?php
								} else {
								    echo Translator::trans('sms.user.sender.unknown');
								}
                                ?></td>
								<td class="ltr"><?php echo $row->sender_number; ?></td>
								<td class="ltr"><?php echo $row->receiver_number->number; ?></td>
								<td><?php echo $row->text; ?></td>
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
		<form id="smslist_search" class="form-horizontal" action="<?php echo userpanel\url('sms/get'); ?>" method="GET">
			<input type="hidden" name="user" value="">
			<?php
            $this->setHorizontalForm('sm-3', 'sm-9');
$feilds = [
    [
        'name' => 'id',
        'type' => 'number',
        'label' => Translator::trans('sms.id'),
        'ltr' => true,
    ],
    [
        'type' => 'hidden',
        'name' => 'sender_user',
    ],
    [
        'name' => 'sender_user_name',
        'label' => Translator::trans('sms.user.sender'),
    ],
    [
        'type' => 'number',
        'name' => 'sender_number',
        'label' => Translator::trans('sms.number.sender'),
        'ltr' => true,
    ],
    [
        'type' => 'hidden',
        'name' => 'receiver_user',
    ],
    [
        'name' => 'text',
        'label' => Translator::trans('sms.text'),
    ],
    [
        'name' => 'status',
        'type' => 'select',
        'label' => Translator::trans('sms.get.status'),
        'options' => $this->getStatusForSelect(),
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
		<button type="submit" form="smslist_search" class="btn btn-success"><?php echo Translator::trans('search'); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo Translator::trans('cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();
