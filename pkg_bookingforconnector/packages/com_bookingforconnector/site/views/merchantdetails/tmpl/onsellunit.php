<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));

$merchant = $this->item;
?>
<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t<?php echo  $this->item->MerchantTypeId?>">
	<?php echo  $this->loadTemplate('head'); ?>
	<?php if ($this->items != null): ?>
	<div class="com_bookingforconnector_merchantdetails-offers">
		<?php foreach($this->items as $onSellUnit): ?>

		<?php
		$onSellUnitName = BFCHelper::getLanguage($onSellUnit->Name, $this->Language);
$this->document->setTitle($onSellUnitName);
$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_SELL_TITLE'),$merchant->Name,$onSellUnitName));
		

		$img = JURI::root() . "media/com_bookingfor/images/default.png";
		$imgError = JURI::root() . "media/com_bookingfor/images/default.png";

		if ($resource->ImageUrl != ''){
			$img = BFCHelper::getImageUrlResized('onsellunits',$onSellUnit->ImageUrl , 'onsellunit_list_default');
			$imgError = BFCHelper::getImageUrl('onsellunits',$onSellUnit->ImageUrl , 'onsellunit_list_default');
		}elseif ($merchant->LogoUrl != ''){
			$img = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'resource_list_default_logo');
			$imgError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'resource_list_default_logo');

		}

		?>
		<div class="com_bookingforconnector_search-resource">
			<div class="com_bookingforconnector_merchantdetails-resource com_bookingforconnector_merchantdetails-resource-open">
				<div class="com_bookingforconnector_merchantdetails-resource-features">
					<a class="com_bookingforconnector_resource-imgAnchor" ><img class="com_bookingforconnector_resource-img" src="<?php echo $img?>" onerror="this.onerror=null;this.src='<?php echo $imgError?>'" /></a>
					<h3 class="com_bookingforconnector_merchantdetails-resource-name"><?php echo  $onSellUnitName ?></h3>
					<p class="com_bookingforconnector_merchantdetails-resource-desc">
						<?php echo  BFCHelper::getLanguage($onSellUnit->Description, $this->language, null, array('ln2br'=>'ln2br')) ?>
					</p>
				</div>
				<div class="clearboth"></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_search-merchant-resource nominheight noborder">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">
					<?php if ($onSellUnit->Price != null && $onSellUnit->Price > 0):?>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-merchantlist-merchant-price minheight34 borderright">
								<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_PRICE')?>: &euro; <?php echo number_format($onSellUnit->Price,2, ',', '.')?>
							</div>
					<?php else: ?>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 minheight34 ">&nbsp;</div>
					<?php endif; ?>
						</div>
				</div>
			</div>
		</div>		
		<br />
		{rsform 5}
		<?php endforeach?>
	</div>
	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php endif?>
</div>
<script type="text/javascript">
jQuery(function($) {
	$('.moduletable-insearch').show();
});
</script>
