<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$order = $this->item;
$urlPayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
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

?>
<!-- <h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DATAORDER') ?></h3> -->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ORDERID') ?></span>: <?php echo $order->OrderId;?>
		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EXTERNALORDERID') ?></span>: <?php echo $order->ExternalId;?>
		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_RESOURCE') ?></span>: <?php echo BFCHelper::getItem($order->NotesData,'nome' ,'unita');?>
		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PERIOD') ?></span>: <?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PERIOD_LABEL'), $dateCheckin, $dateCheckout)?>
		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PERSON') ?></span>: <?php echo BFCHelper::getItem($order->NotesData,'quantita' ,'persone');?>
		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT') ?></span>: <?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT_LABEL'), $order->DepositAmount)?>
		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS') ?></span>: 
			<?php if ($order->Status == 1):?>
				<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_1') ?>
				<a href="<?php echo $urlPayment ?>" class="btn"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAY') ?></a>
			<?php endif;?>
			<?php if ($order->Status == 0 || $order->Status == 20|| $order->Status == 16 || $order->Status == 4):?>
				<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_0') ?>
				<a href="<?php echo $urlPayment ?>" class="btn"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAY') ?></a>
			<?php endif;?>
			<?php if ($order->Status == 7):?>
				<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_7') ?>
				<a href="<?php echo $urlPayment ?>" class="btn"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PAY') ?></a>
			<?php endif;?>
			<?php if ($order->Status == 5):?>
				<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_5') ?>
			<?php endif;?>

		</div><!--/span-->
	</div><!--/row-->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
			<form action="<?php echo $urlCrew?>"  method="post"  >
				<input type="hidden" name="OrderId" value="<?php echo $order->OrderId; ?>" />
				<input type="submit" class="btn" value="<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SENDCREW') ?>" />
			</form>
		</div><!--/span-->
	</div><!--/row-->
<h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DATACUSTOMER') ?></h3>
      <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
        <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">        
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
              <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_FIRSTNAME') ?></span>: <?php echo $firstName;?>
            </div><!--/span-->
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
              <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_LASTNAME') ?></span>: <?php echo $lastName;?>    
            </div><!--/span-->
          </div><!--/row-->
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
              <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_EMAIL') ?></span>: <?php echo $email;?>    
            </div><!--/span-->
          </div><!--/row-->
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
              <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_ADDRESS') ?></span>: <?php echo $address;?>   
            </div><!--/span-->
          </div><!--/row-->
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
               <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CITY') ?></span>: <?php echo $city;?>   
            </div><!--/span-->
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
               <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PROVINCE') ?></span>: <?php echo $province;?>
            </div><!--/span-->
          </div><!--/row-->           
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
               <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_POSTALCODE') ?></span>: <?php echo $postalCode;?>   
            </div><!--/span-->
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
               <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_NATION') ?></span>: <?php echo $nation; ?>
            </div><!--/span-->
          </div><!--/row-->                              
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
               <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_PHONE') ?></span>: <?php echo $phone;?>
            </div><!--/span-->
            <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
               <span><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CULTURE') ?></span>: <?php echo $culture; ?>
            </div><!--/span-->
          </div><!--/row-->
		</div><!--/span-->
	  </div><!--/row-->
