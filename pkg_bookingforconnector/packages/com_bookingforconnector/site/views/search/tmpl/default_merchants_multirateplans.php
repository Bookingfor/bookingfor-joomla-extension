<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchants =& $this->items;
$language = $this->language;
$searchid =  $this->params['searchid'];
$filters = $this->params['filters'];
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$ordselect = array(
	JHTML::_('select.option', 'stay|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC') ),
	JHTML::_('select.option', 'stay|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYDESC')),
	JHTML::_('select.option', 'rating|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STARSASC')),
	JHTML::_('select.option', 'rating|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STARSDESC')),
	JHTML::_('select.option', 'distancefromsea|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEASC')),
	JHTML::_('select.option', 'distancefromsea|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEDESC'))
);

$starselect = array(
	JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_STARS_CHOOSE') ),
	JHTML::_('select.option', '1', '1' ),
	JHTML::_('select.option', '2', '2' ),
	JHTML::_('select.option', '3', '3' ),
	JHTML::_('select.option', '4', '4' ),
	JHTML::_('select.option', '5', '5' )
);

$onchangeOrder = 'onchange="setOrdering(this);"';
$onchangeStars = 'onchange="setStars(this);"';

?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="bookingforsearchForm" id="bookingforsearchForm">
<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	<div class="display-limit display-limit1" style="float:left;text-align:left;">
		Stelle: <?php echo JHTML::_('select.genericlist', $starselect, 'filters[stars]', $onchangeStars, 'value', 'text', $filters['stars'] );?>
	</div>
	<div class="display-limit display-limit2">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>: <?php echo JHTML::_('select.genericlist', $ordselect, 'orderselect', $onchangeOrder, 'value', 'text', strtolower($listOrder.'|'.$listDirn) );?>
	</div>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
	<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
	<input type="hidden" name="limitstart" value="0" />
	<script language="JavaScript">
	<!--
	function setOrdering(option) {
		var form = option.form;
		var order_dir = option.value;
		var vals = order_dir.split("|"); 
		form.filter_order.value = vals[0];
		form.filter_order_Dir.value = vals[1];
		form.submit();
	}
	
	function setStars(option) {
		var form = option.form;
		form.submit();
	}
	//-->
	</script>
</fieldset>
</form>
<div class="com_bookingforconnector-merchantlist">
<?php foreach ($merchants as $merchant):
	$counter = 0;
	$addressData = $merchant->AddressData;
	$merchantLat = $merchant->XGooglePos;
	$merchantLon = $merchant->YGooglePos;
	$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
	
	$route = JRoute::_('index.php?view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));
	$merchantLogo = "/media/com_bookingfor/images/default.png";
	if ($merchant->LogoUrl != '') {
		$merchantLogo = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'merchant_list_default');		
	}

	?>
	<div class="com_bookingforconnector_search-merchant">
		<div class="com_bookingforconnector-merchantlist-merchant com_bookingforconnector-merchantlist-merchant-t<?php echo $merchant->MerchantTypeId?>">
			<div class="com_bookingforconnector-merchantlist-merchant-features">
				<a class="com_bookingforconnector-merchantlist-merchant-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector-merchantlist-logo" src="<?php echo $merchantLogo?>" /></a>
				<h3 class="com_bookingforconnector-merchantlist-merchant-name">
					<a class="com_bookingforconnector-merchantlist-nameAnchor" href="<?php echo $route ?>"><?php echo  $merchant->Name ?></a>
					<span class="com_bookingforconnector_merchantdetails-rating com_bookingforconnector_merchantdetails-rating<?php echo  $merchant->Rating ?>">
						<span class="com_bookingforconnector_merchantdetails-ratingText">Rating <?php echo  $merchant->Rating ?></span>
					</span>
				</h3>
				<div class="com_bookingforconnector-merchantlist-merchant-address">
					<?php if ($addressData != null && $addressData != '') :?>
					<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>:</strong> <?php echo  BFCHelper::getItem($addressData, 'indirizzo') ?> - <?php echo  BFCHelper::getItem($addressData, 'cap') ?> - <?php echo  BFCHelper::getItem($addressData, 'comune') ?> (<?php echo  BFCHelper::getItem($addressData, 'provincia') ?>)
						<?php if ($showMerchantMap):?>
						- <a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_SHOWONMAP') ?></a>
						<?php endif; ?> 
					<?php endif; ?>
				</div>
				<p class="com_bookingforconnector-merchantlist-merchant-desc">
					<?php echo  BFCHelper::getLanguage($merchant->Showcase, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags') ); ?>
				</p>
				<a class="com_bookingforconnector-merchantlist-merchant-moreinfo" href="javascript:void(0);" onclick="toggleDetails(this);"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_LINK')?></a>
			</div>
			<div class="clearboth"></div>
			<div class="com_bookingforconnector_search-merchant-resources">
				<div class="com_bookingforconnector_merchantdetails-resources">
				<?php foreach ($merchant->Resources as $resource):?>
					<?php 
					$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
					$resourceRoute = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
					$allRateplans = $this->getModel()->GetAllRatePlansStay($resource->ResourceId);

					?>
					
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_search-merchant-resource com_bookingforconnector_search-merchant-resource<?php echo $counter % 2 ?>">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>7 borderright">
							<h4 class="com_bookingforconnector_search-merchant-resource-name"><a href="<?php echo $resourceRoute?>"><?php echo BFCHelper::getLanguage($resource->Name, $language) ?></a></h4>
							<?php  if (isset($resource->RatePlanStay)):?>
							<?php 
							 $rateplanName = BFCHelper::getLanguage($resource->RatePlanStay->Name, $this->language);
							 // controlllo se hanno lo stesso nome altrimenti lo visualizzo
							 if ($rateplanName!=$resourceName) {
							?>
								<div class="paddingleft10 tipsyhelp">
									<a href="javascript: void();" data-rel="tipsy" title="<?php echo  BFCHelper::getLanguage($resource->RatePlanStay->Description, $this->language, null, array('striptags'=>'striptags','htmlencode'=>'htmlencode')) ?>"><?php echo  BFCHelper::getLanguage($resource->RatePlanStay->Name, $this->language) ?> </a> 
								</div>
							<?php } ?>							

							<?php endif; ?>							
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector_merchantdetails-resource-paxes">
									<?php if ($resource->MinCapacityPaxes == $resource->MaxCapacityPaxes):?>
										<?php echo  $resource->MaxCapacityPaxes ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_PAXES')?>
									<?php else: ?>
										<?php echo  $resource->MinCapacityPaxes ?>-<?php echo  $resource->MaxCapacityPaxes ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_PAXES')?>
									<?php endif; ?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 borderleft com_bookingforconnector_merchantdetails-resource-rooms">
									<?php echo  $resource->Rooms ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_ROOMS')?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 borderleft com_bookingforconnector_merchantdetails-resource-avail com_bookingforconnector_merchantdetails-resource-avail<?php echo $resource->Stay->Availability ?>"">
									<?php if ($resource->Stay->Availability < 4): ?>
										<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LESSAVAIL'),$resource->Stay->Availability) ?>
									<?php else: ?>
										<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_AVAILABLE')?>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 alignvertical">
							<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),$resource->Stay->Days) ?>
							<div class="com_bookingforconnector_merchantdetails-resource-stay-price">
								<?php if ($resource->Stay->DiscountedPrice < $resource->Stay->TotalPrice): ?>
									<span class="com_bookingforconnector_merchantdetails-resource-stay-discount">&euro; <?php echo $resource->Stay->TotalPrice ?></span>
								<?php endif; ?>
								&euro; <span class="com_bookingforconnector_merchantdetails-resource-stay-total"><?php echo $resource->Stay->DiscountedPrice ?></span>
								<br />
							</div>
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 alignvertical">
							<a class="btn btn-success " href="<?php echo $resourceRoute ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
						</div>
					</div>
					<!-- elenco rateplan -->
					<?php if(count($allRateplans)>1): ?>
						<?php foreach ($allRateplans as $rateplan):?>
							<?php if($rateplan->RatePlanId <> $resource->RatePlanStay->RatePlanId ): ?>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_search-merchant-resource com_bookingforconnector_search-merchant-resource<?php echo $counter % 2 ?>">
									<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>7 borderright">
										<!-- <h4 class="com_bookingforconnector_search-merchant-resource-name"><a href="<?php echo $resourceRoute?>"><?php echo BFCHelper::getLanguage($resource->Name, $language) ?></a></h4> -->
										<div class="paddingleft10 tipsyhelp">
											<a href="javascript: void();" data-rel="tipsy" title="<?php echo  BFCHelper::getLanguage($rateplan->Description, $this->language, null, array('striptags'=>'striptags','htmlencode'=>'htmlencode')) ?>"><?php echo  BFCHelper::getLanguage($rateplan->Name, $this->language) ?></a>   
										</div>
									</div>
									<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 alignvertical">
										<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),$rateplan->SuggestedStay->Days) ?>
										<div class="com_bookingforconnector_merchantdetails-resource-stay-price">
											<?php if ($rateplan->SuggestedStay->DiscountedPrice < $rateplan->SuggestedStay->TotalPrice): ?>
												<span class="com_bookingforconnector_merchantdetails-resource-stay-discount">&euro; <?php echo BFCHelper::priceFormat($rateplan->SuggestedStay->TotalPrice) ?></span>
											<?php endif; ?>
											&euro; <span class="com_bookingforconnector_merchantdetails-resource-stay-total"><?php echo BFCHelper::priceFormat($rateplan->SuggestedStay->DiscountedPrice) ?></span>
										</div>
									</div>
									<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 alignvertical">
										<a class="btn btn-success " href="<?php echo $resourceRoute ?>?pricetype=<?php echo $rateplan->RatePlanId ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
									</div>
								</div>
							<?php endif ?>
						<?php endforeach; ?>

					<?php endif ?>
					<?php
					$counter += 1;
					?>
				<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function toggleDetails(anchor) {
		var c = 'com_bookingforconnector-merchantlist-merchant-open';
		var a = jQuery(anchor);
		var p = a.parents('div.com_bookingforconnector-merchantlist-merchant').first();
		if (p.hasClass(c))
			p.removeClass(c);
		else
			p.addClass(c);
	}
//-->
</SCRIPT>
