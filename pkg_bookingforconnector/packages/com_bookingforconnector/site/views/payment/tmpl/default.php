<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


/*
=====================
	IDpaymentSystem	
=====================
		1	BankPass
		2	Pagoonline
		3	PaymentGateway
		4	ICheckOut
		5	KeyClient
		6	Setefi
		7	WSPayForm
		8	Activa
*/
//$route= JRoute::_('index.php?view=orders&checkmode=' . $checkmode);

$order = $this->item->order;
$merchantPayments = $this->item->merchantPayments;

if (isset( $this->item->merchantPayment)){
	$merchantPayment = $this->item->merchantPayment;
	$paymentSystemId = $merchantPayment->PaymentSystemId;
//	$paymentSystemRef = null;
	$paymentSystemRef = $merchantPayment->PaymentSystemName;

	$actionmode =  $this->actionmode;
	$hasPayed = $this->hasPayed;

	//echo "<pre>";
	//echo $actionmode;
	//echo "</pre>";
	$routeOrderPayed = JRoute::_('index.php?view=orders&actionform=login&checkMode=5&orderid=' . $order->OrderId . '&email=' . BFCHelper::getItem($order->CustomerData, 'email'));
	if (COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM==="GPDati"){
		$dateCheckin = BFCHelper::parseJsonDate($order->StartDate);

		$routeOrderPayed = JRoute::_('index.php?view=orders&actionform=login&checkMode=146&externalOrderId=' . $order->ExternalId . '&customerLastname=' . BFCHelper::getItem($order->CustomerData, 'cognome') . '&checkIn='.$dateCheckin);
	}

	?>
		<?php if ($actionmode=='donation'):?>
			<?php
			$routeOrderPayed = JRoute::_('index.php?view=orders&actionmode=donation');

			//echo "<pre>merchantPayment:<br />";
			//echo print_r($merchantPayment);
			//echo "</pre>";

				if(!empty($paymentSystemRef)){
					echo  $this->loadTemplate('payment_'.$paymentSystemRef);  
				}
			?>
		<?php else:?>

			<?php if ($hasPayed!==null):?>
				<?php if ($hasPayed):?>
					<p class="success">
						<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_PAY_OK') ?>
						<!-- <br/>
						<a href="<?php echo $routeOrderPayed?>" ><?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_PRINT') ?></a> -->
					</p>
				<?php else:?>
					<p class="error">
						<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_PAY_KO') ?>
					</p>
				<?php endif;?>
			<?php else:?>
				<?php if ($actionmode=='cancel'):?>
					<p class="error">
						<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_PAY_KO_DELETE') ?>
					</p>
					<br />
				<?php endif;?>
				<?php if ($actionmode=='error' || $actionmode=='errordonation'):?>
					<p class="error">
						<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_PAY_KO_ERROR') ?>
					</p>
					<br />
				<?php endif;?>
				<?php if ($actionmode!='errordonation' && $order!=null && $order->DepositAmount>0 && $paymentSystemRef!=null) :?>
					<!-- Form normale <br/>
					IDordine = <?php echo $order->OrderId?><br />
					tipologia di pagamento: <?php echo $paymentSystemRef ?><br /> -->
					<?php if ($actionmode!='error'):?>
						<?php echo  $this->loadTemplate('payment_'.$paymentSystemRef);  ?>
					<?php endif;?>
				<?php else:?>
					<?php if ($actionmode!='errordonation'):?>
						<p class="error">
							<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_ERROR') ?>:
								<?php if ($order==null):?>  
								<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_ERROR_NOORDER') ?> <br />
								<?php endif;?>
								<?php if ($order!=null && $order->DepositAmount<1):?>
								<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_ERROR_NODEPOSIT') ?><br />
								<?php endif;?>
								<?php if ($paymentSystemRef==null ):?>
								<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_ERROR_NOPAYMENTSYSTEM') ?><br />
								<?php endif;?>
						</p>
					<?php endif;?>
				<?php endif;?>
			<?php endif;?>
		<?php endif;?>
	<?php }else{?>
		<?php echo JText::_('COM_BOOKINGFORCONNECTOR_PAYMENT_VIEW_ERROR_NOPAYMENTSYSTEM') ?><br />
	<?php }?>
