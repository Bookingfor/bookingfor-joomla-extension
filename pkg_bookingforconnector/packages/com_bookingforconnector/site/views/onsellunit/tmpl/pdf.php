<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
$merchant = $resource->Merchant;

$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);

$addressData = $resource->AddressData;
$merchantAddress = false;
if ($addressData == '' || $addressData == null || $merchant->MerchantTypeId != 2) {
	$addressData = $merchant->AddressData;
	$merchantAddress = true;
}

$distanceFromSea = $resource->DistanceFromSea;
$distanceFromCenter = $resource->DistanceFromCenter;

if ($distanceFromSea == 0 || $distanceFromCenter == 0) {
	$distanceFromSea = $merchant->DistanceFromSea;
	$distanceFromCenter = $merchant->DistanceFromCenter;
}

$maxCapacityPaxes = $resource->MaxCapacityPaxes;
$minCapacityPaxes = $resource->MinCapacityPaxes;

$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$merchantRules = BFCHelper::getLanguage($merchant->Rules, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
$resourceRules = BFCHelper::getLanguage($resource->Rules, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));

?>
<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> com_bookingforconnector_resource-t<?php echo  $resource->MasterTypologyId?>">
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> 
		<span class="com_bookingforconnector_resource-rating com_bookingforconnector_resource-rating<?php echo  $merchant->Rating ?>">
			<span class="com_bookingforconnector_resource-ratingText">Rating <?php echo  $merchant->Rating ?></span>
		</span>
	</h2>
	<div class="com_bookingforconnector_resource-address">
		<?php if ($merchantAddress):?>
		<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo  BFCHelper::getItem($addressData, 'indirizzo') ?> - <?php echo  BFCHelper::getItem($addressData, 'cap') ?> - <?php echo  BFCHelper::getItem($addressData, 'comune') ?> (<?php echo  BFCHelper::getItem($addressData, 'provincia') ?>)		
		<?php  else:?>
		<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo  $addressData ?>
		<?php endif;?>
	</div>	
	<div class="com_bookingforconnector_resource-description">
		<h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h3>
		<?php echo  BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
	</div>
	<div id="calculator" class="ajaxReload"><?php echo  $this->loadTemplate( $resource->HasRateplans ? 'calculator_rateplan' : 'calculator'); ?></div>	
	<div class="com_bookingforconnector_resource-data">
		<h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FEATURES') ?></h3>
		<table>
			<tr>
				<td>
					<?php if ($maxCapacityPaxes == $minCapacityPaxes):?>
						<?php echo  $resource->MaxCapacityPaxes ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FEATURES_PAXES')?>
					<?php else: ?>
						<?php echo  $minCapacityPaxes ?>-<?php echo  $maxCapacityPaxes ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FEATURES_PAXES')?>
					<?php endif; ?>
				</td>
				<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FEATURES_CENTERDISTANCE') ?>: <?php echo  $distanceFromCenter ?></td>
				<td><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FEATURES_SEADISTANCE') ?>: <?php echo  $distanceFromSea ?></td>
			</tr>
		</table>
	</div>
	<?php if (count($resource->Services) > 0):?>
	<div class="com_bookingforconnector_resource-services">
		<h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></h3>
		<?php $count=0; ?>
		<?php foreach ($resource->Services as $service):?>
			<?php
			if ($count > 0) { 
				echo ',';
			}
			?>			
			<span class="com_bookingforconnector_resource-services-service"><?php echo BFCHelper::getLanguage($service->Name, $this->language) ?></span>
			<?php $count += 1; ?>
		<?php endforeach?>
	</div>
	<?php endif; ?>
	<?php if ( ($merchantRules != null && $merchantRules != '') || ($resourceRules != null && $resourceRules != '') ):?>
	<div class="com_bookingforconnector_resource-conditions">
		<h3><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CONDITIONS') ?></h3>
		<?php echo $resourceRules != '' ? $resourceRules : $merchantRules; ?>
	</div>
	<?php endif; ?>
</div>
<script type="text/javascript">
jQuery(function($) {
	$('.moduletable-insearch').show();
});
</script>
