<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus"></i>
                <span><?php echo translator::trans("sms.send");?></span>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
            </div>
            <div class="panel-body">
                <form id="send_sms" action="<?php echo userpanel\url('sms/send') ?>" method="post">
					<?php
					$this->createField(array(
						'type' => 'select',
						'name' => 'from',
						'label' => translator::trans("sms.number.sender"),
						'ltr' => true,
						'options' => $this->getNumbersForSelect()
					));
					$this->createField(array(
						'name' => 'to',
						'label' => translator::trans("sms.number.reciver"),
						'ltr' => true
					));
					$this->createField(array(
						'type' => 'textarea',
						'name' => 'text',
						'class' => 'autosize form-control',
						'label' => translator::trans('sms.text')
					));
					?>
					<hr>
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<button class="btn btn-success btn-block" type="submit"><i class="fa fa-paper-plane"></i><?php echo translator::trans("send"); ?></button>
						</div>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->the_footer();
