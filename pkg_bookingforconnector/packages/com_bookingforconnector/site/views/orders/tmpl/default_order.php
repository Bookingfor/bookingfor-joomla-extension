<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');



$config = $this->config;
$isportal = $config->get('isportal', 1);
$usessl = $config->get('usessl', 0);
$ssllogo = $config->get('ssllogo','');
$sitename = $this->sitename;

$cCCTypeList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCTYPELIST'));
$formRoute = "index.php?option=com_bookingforconnector&task=updateCCdataOrder"; 
$minyear = date("y");
$maxyear = $minyear+5;

$order = $this->item;
$urlPayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
$urlOtherPayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
$urlCrew = JRoute::_('index.php?view=crew&orderId=' . $order->OrderId);

$dateCheckin = BFCHelper::parseJsonDate($order->StartDate);
$dateCheckout = BFCHelper::parseJsonDate($order->EndDate);

$firstName = BFCHelper::getItem($order->CustomerData, 'nome');
$lastName = BFCHelper::getItem($order->CustomerData, 'cognome');
$email = BFCHelper::getItem($order->CustomerData, 'email');
$nation = BFCHelper::getItem($order->CustomerData, 'stato');
$culture = BFCHelper::getItem($order->CustomerData, 'lingua');
$address = BFCHelper::getItem($order->CustomerData, 'indirizzo');
$city = BFCHelper::getItem($order->CustomerData, 'citta');
$postalCode = BFCHelper::getItem($order->CustomerData, 'cap');
$province = BFCHelper::getItem($order->CustomerData, 'provincia');
$phone = BFCHelper::getItem($order->CustomerData, 'telefono');
$ArchivedAsSpam = $order->ArchivedAsSpam;

			if(empty($order->DepositAmount)){
				$order->DepositAmount = $order->TotalAmount;
			}

?>
<!-- <h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DATAORDER') ?></h3> -->

	<?php if ($ArchivedAsSpam) :?>
		<div class="alert alert-error">
			<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SPAM') ?></strong>
		</div>
	<?php endif; ?>
	<table class="table table-striped">
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_NAME') ?></td>
			<td><?php echo $firstName;?> <?php echo $lastName;?>    </td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?></td>
			<td><?php echo $email;?>    </td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?></td>
			<td><?php echo $order->OrderId;?></td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></td>
			<td><?php echo $order->ExternalId;?></td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_RESOURCE') ?></td>
			<td><?php echo BFCHelper::getItem($order->NotesData,'nome' ,'unita');?></td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PERIOD') ?></td>
			<td><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PERIOD_LABEL'), $dateCheckin, $dateCheckout)?></td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PERSON') ?></td>
			<td><?php if (!$ArchivedAsSpam) :?>
				<form action="<?php echo $urlCrew?>"  method="post" class="form-inline" style="margin-bottom:0" >
				<?php echo $order->PaxNumber;?>
					<input type="hidden" name="OrderId" value="<?php echo $order->OrderId; ?>" />
					<input type="submit" class="btn bfi_btncrew" value="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SENDCREW') ?>" />
				</form>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_TOTAL') ?></td>
			<td><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT_LABEL'), $order->TotalAmount)?></td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT') ?></td>
			<td><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT_LABEL'), $order->DepositAmount)?></td>
		</tr>
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS') ?></td>
			<td>
	<?php if ($ArchivedAsSpam) :?>
				<div class="alert alert-error">
					<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SPAM') ?></strong>
				</div>
	<?php else: ?>
				<?php if ($order->Status == 1 && $order->DepositAmount >0):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_1') ?>
					<a href="<?php echo $urlPayment ?>" class="btn "><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAY') ?></a>
				<?php endif;?>
				<?php if (($order->Status == 0)  && $order->DepositAmount >0):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_0') ?>
				<?php endif;?>
				<?php if (( $order->Status == 16 || $order->Status == 4)  && $order->DepositAmount >0):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_0') ?>
					<a href="<?php echo $urlPayment ?>" class="btn "><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAY') ?></a>
				<?php endif;?>
				<?php if ($order->Status == 7 && $order->DepositAmount >0):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_7') ?>
					<a href="<?php echo $urlPayment ?>" class="btn "><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAY') ?></a>
				<?php endif;?>
				<?php if ($order->Status == 3):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_3') ?>
				<?php endif;?>
				<?php if ($order->Status == 5):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_5') ?>
				<?php endif;?>
				<?php if ($order->Status == 20):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_20') ?><br />
					<?php 
						$ccDecr1=BFCHelper::decrypt($order->CCdata);
						$ccDecr= substr($ccDecr1, 0, strrpos($ccDecr1,">")+1);
						$cc = new StdClass();
						$cc->Type = BFCHelper::getItem($ccDecr, 'tipo','cc');
						$cc->Name = BFCHelper::getItem($ccDecr, 'nome','cc');
						$cc->Number = BFCHelper::getItem($ccDecr, 'numero','cc');
						$cc->ExpiryMonth = BFCHelper::getItem($ccDecr, 'expmon','cc');
						$cc->ExpiryYear = BFCHelper::getItem($ccDecr, 'expyear','cc');
//						$cc = json_decode(substr($ccDecr, 0, strrpos($ccDecr,"}")+1));

					?>
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCSUBTITLE'); ?><br /><br />
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCTYPE'); ?>: </span>
								<?php echo $cc->Type; ?>
							</div><!--/span-->
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNAME'); ?>: </span>
								<?php echo $cc->Name; ?>
							</div><!--/span-->
						</div>
						
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNUMBER'); ?>: </span>
								<?php 
//									echo 'xxxx-xxxx-xxxx-' . substr($cc->Number, -4) ; 
 echo $cc->Number;
							 ?>
							</div><!--/span-->
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCVALIDATION'); ?>: </span>
								<?php echo $cc->ExpiryMonth; ?>/<?php echo $cc->ExpiryYear; ?>
								</div><!--/row-->
							</div><!--/span-->
						</div>

						
						<a href="javascript:changeCC();" class="btn "><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CHANGECC') ?></a>
						<?php endif;?>
	<?php endif; ?>

			</td>
		</tr>
	</table>	
				<?php if ($order->Status == 20 && !$ArchivedAsSpam):?>
<?php 
		$db   = JFactory::getDBO();
		$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
		$uriMerchant.='&merchantId=' . $order->MerchantId . ':';

		if ($itemIdMerchant<>0)
			$uriMerchant.='&Itemid='.$itemIdMerchant;

		$uriMerchantthanks = $uriMerchant .'&layout=thanks';
		$uriMerchantthanksKo = $uriMerchant .'&layout=errors';
		$routeThanks = JRoute::_($uriMerchantthanks);
		$routeThanksKo = JRoute::_($uriMerchantthanksKo);

		$currentbookingTypeId= BFCHelper::getOrderMerchantPaymentId($order);
		$data = BFCHelper::getMerchantPaymentData($currentbookingTypeId);
		if(!empty($data)){
			$cCCTypeList = array();
			$datas = explode("|", $data->Data);
			if (is_array($datas)){
				foreach ($datas as $singleData) {
					$cCCTypeList[] = JHTML::_('select.option', $singleData, $singleData);
				}
			}else{
				$cCCTypeList[] = JHTML::_('select.option', $datas, $datas);
			}
 		}
?>

					<form method="post" id="ccdataupdate" class="form-validate" action="<?php echo $formRoute; ?>" style="display:none;">
							<br /><hr />
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
									<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8" style="border-right:1px solid #cccccc">
										<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCSUBTITLE'); ?><br /><br />
										<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCTYPE'); ?> </label>
												<?php echo JHTML::_('select.genericlist',$cCCTypeList, 'form[cc_circuito]','class="'. COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL .'10"','value', 'text', null) ?>
											</div><!--/span-->
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNAME'); ?> </label>
												<input type="text" value="" size="50" name="form[cc_titolare]" id="cc_titolare" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
											</div><!--/span-->
										</div>
										
										<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNUMBER'); ?> </label>
												<input type="text" value="" size="50" maxlength="50" name="form[cc_numero]" id="cc_numero" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
											</div><!--/span-->
											<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
												<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCVALIDATION'); ?><br />
												<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
													<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
														<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYMONTH'); ?>
													</div><!--/span-->
													<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
														<input type="text" value="" size="2" maxlength="2" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5" name="form[cc_mese]" id="cc_mese" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
														/
														<input type="text" value="" size="2" maxlength="2" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5" name="form[cc_anno]" id="cc_anno" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
													</div><!--/span-->
													<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
														<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYYEAR'); ?>
													</div><!--/span-->
												</div><!--/row-->
											</div><!--/span-->
										</div>
									
									</div><!--/span-->
									<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
										<?php echo$ssllogo ?>
									</div><!--/span-->
							</div>
							<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
							<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
							<input type="hidden" name="OrderId" value="<?php echo $order->OrderId; ?>" />
<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>
							<button type="submit" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button>
</form>
<script type="text/javascript">
<!--
function changeCC(){
	jQuery("#ccdataupdate").show();
};
jQuery(function($){
			$("#ccdataupdate").validate(
		    {
				rules: {
					"form[cc_mese]": {
					  required: true,
					  range: [1, 12]
					},
					"form[cc_anno]": {
					  required: true,
					  range: [<?php echo $minyear ?>, <?php echo $maxyear ?>]
					},
					"form[cc_numero]": {
					  required: true,
					  creditcard: true
					}
				},
		        messages:
		        {
		        	"form[cc_mese]": "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYMONTH_ERROR') ?>",
		        	"form[cc_anno]": "<?php echo  sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYYEAR_ERROR'),$minyear,$maxyear) ?>",
		        	"form[cc_numero]": "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNUMBER_ERROR') ?>"
		        },

				invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        //errorPlacement: function(error, element) { //just nothing, empty  },
				highlight: function(label) {
			    	//$(label).removeClass('error').addClass('error');
			    	//$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
					$(label).remove();
//					$(label).hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {
					if (typeof grecaptcha === 'object') {
						var response = grecaptcha.getResponse();
						//recaptcha failed validation
						if(response.length == 0) {
							$('#recaptcha-error').show();
							return false;
						}
						//recaptcha passed validation
						else {
							$('#recaptcha-error').hide();
						}					 
					}
					 jQuery.blockUI();
					 form.submit();
				}

			});
	});
	
//-->
</script>
				<?php endif;?>

	<?php if ($order->Status == 5):?>
	<!-- ulteriori pagamenti -->
		 <h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERPAYMENTS') ?></h3>
<?php
$orderPayments = BFCHelper::getOrderPayments(0,0,$order->OrderId);
?>
	<?php if (count($orderPayments)>0):?>
	<table class="table table-striped">
		<tr>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYMENTDATE') ?></td>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYMENTAMOUNT') ?></td>
			<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS') ?></td>
		</tr>

		<?php foreach($orderPayments as $orderPayment): ?>
		<?php 
		$datePaymentDate = BFCHelper::parseJsonDate($orderPayment->PaymentDate );
		?>
		<tr>
			<td><?php echo  $datePaymentDate?></td>
			<td><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT_LABEL'), $orderPayment->Value) ?></td>
			<td>
				<?php if ($orderPayment->Status == 7):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_7') ?>
				<?php endif;?>
				<?php if ($orderPayment->Status == 5):?>
					<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_5') ?>
				<?php endif;?>
		</tr>
		
		<?php endforeach?>
	</table>	
	<?php endif;?>
				
				<form action="<?php echo $urlOtherPayment?>"  method="post" class="form-inline" style="margin-bottom:0" id="otherpayment">
					<input type="text" name="overrideAmount" id="overrideAmount" value="" placeholder="0.00" />
					<input type="hidden" name="actionmode" value="orderpayment" />
					<input type="hidden" name="OrderId" value="<?php echo $order->OrderId; ?>" />
					<input type="submit" class="btn btn-primary" value="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERPAYMENTS_PAY') ?>" />
				</form>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#otherpayment").validate(
		    {
		        rules:
		        {
		        	overrideAmount: {
						required: true,
						TwoDecimal: true,
						MinDecimal: true
						}
		        },
		        messages:
		        {
		        	overrideAmount: {
						required:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYIMPORT_ERROR') ?>",
						MinDecimal:"<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYIMPORT_ERROR') ?>",
						TwoDecimal: "<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAYIMPORT_ERROR_TWODECIMAL') ?>"
						}
				},
		        highlight: function(label) {
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.text('ok!').addClass('valid')
			    		.closest('.control-group').removeClass('error').addClass('success');
			    }
		    });
			//$("#overrideAmount").mask("9999?.99",{placeholder:"0"});   
		});

</script>	
	<?php endif;?>
