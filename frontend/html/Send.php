<?php
use packages\base\Translator;
use packages\userpanel;

$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus"></i>
                <span><?php echo t('sms.send'); ?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <form id="send_sms" action="<?php echo userpanel\url('sms/send'); ?>" method="post">
					<?php
                    $this->createField([
                        'type' => 'select',
                        'name' => 'from',
                        'label' => t('sms.number.sender'),
                        'ltr' => true,
                        'options' => $this->getNumbersForSelect(),
                    ]);
$this->createField([
    'name' => 'to',
    'label' => t('sms.number.reciver'),
    'ltr' => true,
]);
$this->createField([
    'type' => 'textarea',
    'name' => 'text',
    'class' => 'autosize form-control',
    'label' => t('sms.text'),
]);
?>
					<hr>
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<button class="btn btn-success btn-block" type="submit"><i class="fa fa-paper-plane"></i><?php echo t('send'); ?></button>
						</div>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->the_footer();
