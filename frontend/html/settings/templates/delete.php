<?php
use \packages\base;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \themes\clipone\utility;

$this->the_header();
$template = $this->getTemplate();
?>
<div class="row">
	<div class="col-md-12">
		<form action="<?php echo userpanel\url('settings/sms/templates/delete/'.$template->id); ?>" method="POST" role="form" class="form-horizontal">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo translator::trans('attention'); ?>!</h4>
				<p>
					<?php echo translator::trans("sms.template.delete.warning", array('template' => $template->id)); ?>
				</p>
				<p>
					<a href="<?php echo userpanel\url('settings/sms/templates'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('back'); ?></a>
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o tip"></i> <?php echo translator::trans("delete") ?></button>
				</p>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();
