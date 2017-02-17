<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();

// list can be grouped by rating only if typeId = "Hotels" and  rating = "all"
$grouped = ($this->params['typeId'] == 1) &&  ($this->params['rating'] == 0);
$currentgroup = 0;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$language = $this->language;

$db   = JFactory::getDBO();
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
	//-------------------pagina per il redirect di tutti i merchant
	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
	$itemIdMerchant = intval($db->loadResult());
	//-------------------pagina per il redirect di tutti i merchant


?>
<h1><?php echo $activeMenu->title?></h1>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
</fieldset>
</form>

<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t">

	<?php if ($this->items != null): ?>
	<div class="com_bookingforconnector_merchantdetails-resources">
		<?php foreach($this->items as $offer): ?>
			<?php
			// assign the current offer to a property so it will be available inside template 'offer'
			$this->item = $offer; 
			$this->uriMerchant = $uriMerchant;
			$this->itemIdMerchant = $itemIdMerchant;

			?>
			<?php echo  $this->loadTemplate('offer'); ?>
		<?php endforeach?>
		<?php if ($this->pagination->get('pages.total') > 1) : ?>
			<div class="pagination">
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
<script type="text/javascript">
jQuery(function($) {
	var shortenOption = {
			moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
			lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
			showChars: '150'
	};
	jQuery(".com_bookingforconnector_merchantdetails-resource-desc").shorten(shortenOption);

});
</script>
