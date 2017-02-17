<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();

// list can be grouped by rating only if typeId = "Hotels" and  rating = "all"
$grouped = ($this->params['typeId'] == 1) &&  ($this->params['rating'] == 0);
$currentgroup = 0;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<h1><?php echo $activeMenu->title?></h1>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<fieldset class="filters">
	<!-- <legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend> -->
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
</fieldset>
</form>
<div class="com_bookingforconnector-merchantlist">
<?php foreach ($this->items as $item) : ?>
	<?php 
	$merchant = $item;
	$addressData = $merchant->AddressData;
	
	$merchantLat = $merchant->XGooglePos;
	$merchantLon = $merchant->YGooglePos;
	$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
	
	$merchantSiteUrl = '';
	if ($merchant->SiteUrl != '') {
		$merchantSiteUrl =$merchant->SiteUrl;
		if (strpos('http://', $merchantSiteUrl) == false) {
			$merchantSiteUrl = 'http://' . $merchantSiteUrl;
		}
		$merchantSiteUrlstripped = str_replace('http://', "", $merchantSiteUrl);
		if (strpos($merchantSiteUrlstripped,'?') !== false) {
			$tmpurl = explode("?",$merchantSiteUrlstripped);
			$merchantSiteUrlstripped = $tmpurl[0];
		}
	}


	$route = JRoute::_('index.php?view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));
//	echo "<pre>";
//	echo $route;
//	echo "</pre>";
	?>
	<div class="com_bookingforconnector-merchantlist-merchant com_bookingforconnector-merchantlist-merchant-t<?php echo $merchant->MerchantTypeId?>">
		<div class="com_bookingforconnector-merchantlist-merchant-features">
			<div class="pull-left">
				<?php if ($merchant->LogoUrl != '') :?>
				<a class="com_bookingforconnector-merchantlist-merchant-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector-merchantlist-logo" src="<?php echo  BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'merchant_list_default')?>" /></a>
				<?php else :?>
				<a class="com_bookingforconnector-merchantlist-merchant-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector-merchantlist-logo" src="<?php echo JURI::base()?>images/default.png" /></a>
				<?php endif ?><br />
				{bfcrating <?php echo $merchant->MerchantId?>}
			</div>
			<h3 class="com_bookingforconnector-merchantlist-merchant-name">
				<a class="com_bookingforconnector-merchantlist-nameAnchor" href="<?php echo $route ?>"><?php echo  $merchant->Name ?></a>
				<span class="com_bookingforconnector_merchantdetails-rating com_bookingforconnector_merchantdetails-rating<?php echo  $merchant->Rating ?>">
					<span class="com_bookingforconnector_merchantdetails-ratingText">Rating <?php echo  $merchant->Rating ?></span>
				</span>
			</h3>
			<div class="com_bookingforconnector-merchantlist-merchant-address">
				<?php if ($addressData != null && $addressData != '') :?>
				<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>:</strong> <?php echo  BFCHelper::getItem($addressData, 'indirizzo') ?> - <?php echo  BFCHelper::getItem($addressData, 'cap') ?> - <?php echo  BFCHelper::getItem($addressData, 'comune') ?> (<?php echo  BFCHelper::getItem($addressData, 'provincia') ?>)
					<?php if ($showMerchantMap && false):?> 
					- <?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_SHOWMAP') ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<p class="com_bookingforconnector-merchantlist-merchant-desc com_bookingforconnector-merchantlist-merchant-desc-ext">
				 {bfcmerchantgroup <?php echo $merchant->MerchantId?>}
				<?php echo  BFCHelper::getLanguage($merchant->Showcase, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags') ); ?>
			</p>
			<a class="com_bookingforconnector-merchantlist-merchant-moreinfo" href="javascript:void(0);" onclick="toggleDetails(this);"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_LINK')?></a>
		</div>

		<div class="clearboth"></div>
		<div class="row-fluid com_bookingforconnector_search-merchant-resource nominheight noborder">
			<div class="row-fluid ">
				<div class="span8 com_bookingforconnector-merchantlist-merchant-phone minheight34 ">
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_PHONE')?>: <?php echo BFCHelper::getItem($item->ContactData, 'telefono1') ?>
					<!-- - <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_FAX')?>: <?php echo BFCHelper::getItem($item->ContactData, 'fax') ?> -->
					<?php if ($merchantSiteUrl != ''):?> 
					- <?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=redirect&view=merchantdetails&tmpl=component&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_SITEGO') ,'target="_blank"') ?>
					<!-- - <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_URL')?>: <a href="<?php echo $merchantSiteUrl?>" target="_blank"><?php echo $merchant->SiteUrl ?></a> -->
					<?php endif; ?>
				</div>
				<div class="span4">
					<a class="btn btn-info pull-right" href="<?php echo $route ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_BUTTON')?></a>
				</div>
			</div>
		</div>
	</div>
	<br /><br />
<?php endforeach; ?>
</div>
<div class="clearboth"></div>
<br />
<?php if ($this->pagination->get('pages.total') > 1) : ?>
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
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