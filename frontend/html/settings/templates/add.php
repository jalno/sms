<?php
use \packages\base;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\userpanel\Date;
use \themes\clipone\Utility;

$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus"></i>
                <span><?php echo Translator::trans("settings.sms.templates.add"); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <form class="tempates_form" action="<?php echo userpanel\url('settings/sms/templates/add'); ?>" method="post">
					<div class="col-md-6">
						<?php
						$this->createField(array(
							'type' => 'select',
							'name' => 'name',
							'label' => Translator::trans("sms.template.name"),
							'options' => $this->getTemplatesForSelect()
						));
						$this->createField(array(
							'type' => 'select',
							'name' => 'lang',
							'label' => Translator::trans("sms.template.lang"),
							'options' => $this->getLanguagesForSelect()
						));

						$this->createField(array(
							'type' => 'textarea',
							'name' => 'text',
							'label' => Translator::trans("sms.template.text"),
							'rows' => 4
						));
						$this->createField(array(
							'name' => 'status',
							'type' => 'select',
							'label' => Translator::trans("sms.template.status"),
							'options' => $this->getTemplateStatusForSelect()
						));
						?>
					</div>
					<div class="col-md-6">
						<div class="table-responsive container-table-variables">
			                <table class="table table-variables">
								<thead>
									<tr>
										<th><?php echo Translator::trans('sms.template.variable.key'); ?></th>
										<th><?php echo Translator::trans('sms.template.variable.description'); ?></th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-12">
		                <p>
		                    <a href="<?php echo userpanel\url('settings/sms/templates'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo Translator::trans('return'); ?></a>
		                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo Translator::trans("submit"); ?></button>
		                </p>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$this->the_footer();
