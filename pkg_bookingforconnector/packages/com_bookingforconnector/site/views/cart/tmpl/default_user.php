<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$params = $this->params;
$document = $this->document;
$language 	= $this->language;

$user = JFactory::getUser();

JHtml::_('behavior.keepalive');

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=user';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$uri.='&Itemid='.$itemId;
$uriOrder = 'index.php?option=com_bookingforconnector&view=orders&checkmode=1';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriOrder .'%' ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$uriOrder.='&Itemid='.$itemId;

$return = urlencode(JRoute::_($uri));
$returnOrder = JRoute::_($uriOrder);

?>   
<!-- {emailcloak=off} -->
<div class="container-fluid">  
<form action="<?php echo JRoute::_(htmlspecialchars(JUri::getInstance()->toString()), true); ?>" method="post" id="login-form" class="form-vertical">
		<?php echo JText::sprintf('COM_BOOKINGFORCONNECTOR_USER_VIEW_HELLO', htmlspecialchars($user->get('name'))); ?>
	<div class="logout-button">
		<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return ?>" />
		<input type="hidden" name="view" value="login" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
	<?php if ($this->items != null): ?>
	<div class="com_bookingforconnector_merchantdetails-resources">
			<table class="table">
				<tr>
					<td>OrderId</td>
					<td>Risorsa</td>
					<td>Struttura</td>
					<td>Periodo</td>
					<td>Status</td>
					<td>Action</td>
				</tr>
		<?php foreach($this->items as $order): ?>
			<?php
			// assign the current offer to a property so it will be available inside template 'offer'
				$currClass="";
				$currStatus="";
				if ($order->Status == 0 || $order->Status == 16){
					$currClass="info";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_0');
				}
				if ($order->Status == 5 || $order->Status == 20){
					$currClass="success";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_5');
				}
				if ($order->Status == 1){
					$currClass="warning";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_1');
				}
				if ($order->Status == 4){
					$currClass="warning";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_4');
				}
				if ($order->Status == 7){
					$currClass="warning";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_7');
				}
				if ($order->Status == 3){
					$currClass="error";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_STATUS_3');
				}
				if ($order->ArchivedAsSpam){
					$currClass="error";
					$currStatus=JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_SPAM');
				}			
			?>
				<tr class="<?php echo $currClass; ?>">
					<td><?php echo $order->OrderId ?></td>
					<td><?php echo $order->ResName ?></td>
					<td><?php echo $order->MrcName ?></td>
					<td><?php echo $order->MrcName ?></td>
					<td><?php echo  $currStatus ?></td>
					<td><form method="post" action="<?php echo $returnOrder; ?>" class="form-inline">
						<?php echo JHtml::_('form.token'); ?>
						<input type="hidden" id="orderId" name="orderId" value="<?php echo $order->OrderId ?>" />
						<input type="hidden" id="actionform" name="actionform" value="login"/>
						<input type="submit" value="Edit" />
					</form></td>
				</tr>
		<?php endforeach?>
			</table>
		<?php if ($this->pagination->get('pages.total') > 1) : ?>
			<div class="pagination bfi-pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	</div>	
	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php endif?>	

</div>
