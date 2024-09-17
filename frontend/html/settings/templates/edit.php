<?php
use packages\base\Translator;
use packages\userpanel;

$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-edit"></i>
                <span><?php echo t('settings.sms.templates.edit'); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <form class="tempates_form" action="<?php echo userpanel\url('settings/sms/templates/edit/'.$this->getTemplate()->id); ?>" method="post">
					<div class="col-md-6">
						<?php
                        $this->createField([
                            'type' => 'select',
                            'name' => 'name',
                            'label' => t('sms.template.name'),
                            'options' => $this->getTemplatesForSelect(),
                        ]);
$this->createField([
    'type' => 'select',
    'name' => 'lang',
    'label' => t('sms.template.lang'),
    'options' => $this->getLanguagesForSelect(),
]);

$this->createField([
    'type' => 'textarea',
    'name' => 'text',
    'label' => t('sms.template.text'),
    'rows' => 4,
]);
$this->createField([
    'name' => 'status',
    'type' => 'select',
    'label' => t('sms.template.status'),
    'options' => $this->getTemplateStatusForSelect(),
]);
?>
					</div>
					<div class="col-md-6">
						<div class="table-responsive container-table-variables">
			                <table class="table table-variables">
								<thead>
									<tr>
										<th><?php echo t('sms.template.variable.key'); ?></th>
										<th><?php echo t('sms.template.variable.description'); ?></th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-12">
		                <p>
		                    <a href="<?php echo userpanel\url('settings/sms/templates'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo t('return'); ?></a>
		                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?php echo t('submit'); ?></button>
		                </p>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$this->the_footer();
