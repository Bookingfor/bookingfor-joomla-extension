<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$language = $this->language;
$offers = $this->items;
$listNameAnalytics = $this->listNameAnalytics;
$fromsearchparam = "&lna=".$listNameAnalytics;

$db   = JFactory::getDBO();
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
	//-------------------pagina per il redirect di tutti i merchant
	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
	$itemIdMerchant = intval($db->loadResult());
	//-------------------pagina per il redirect di tutti i merchant

$total = $this->pagination->total;

?>
<h1><?php echo $activeMenu->title?></h1>
<div class="bfi-content">
<div class="bfi-search-menu">
	<div class="bfi-view-changer">
		<div class="bfi-view-changer-selected"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
		<div class="bfi-view-changer-content">
			<div id="list-view"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div id="grid-view" class="bfi-view-changer-grid"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
		</div>
	</div>
</div>
<div class="bfi-clearfix"></div>
	<?php if ($offers != null){ ?>
		<div id="bfi-list" class="bfi-row bfi-list">
			<?php foreach($offers as $resource){ ?>
			<?php
		$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		
		$currUriMerchant = $uriMerchant. '&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MrcName);
		if ($itemIdMerchant<>0)
			$currUriMerchant.='&Itemid='.$itemIdMerchant;
		$resourceRoute  = JRoute::_($currUriMerchant.'&layout=offer&offerId=' . $resource->VariationPlanId . ':' . BFCHelper::getSlug($resourceName).$fromsearchparam);
		
		if(!empty($resource->DefaultImg)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('variationplans',$resource->DefaultImg, 'medium');
		}

			?>
				<div class="bfi-col-sm-6 bfi-item">
					<div class="bfi-row bfi-sameheight" >
						<div class="bfi-col-sm-3 bfi-img-container">
							<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;'><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
						</div>
						<div class="bfi-col-sm-9 bfi-details-container">
							<!-- merchant details -->
							<div class="bfi-row" >
								<div class="bfi-col-sm-10">
									<div class="bfi-item-title">
										<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->VariationPlanId?>" target="_blank"><?php echo  $resource->Name ?></a> 
									</div>
									<div class="bfi-description"><?php echo $resourceDescription ?></div>
								</div>
							</div>
							<div class="bfi-clearfix bfi-hr-separ"></div>
							<!-- end merchant details -->
							<!-- resource details -->
							<div class="bfi-row" >
								<div class="bfi-col-sm-8">
								
								</div>
								<div class="bfi-col-sm-4 bfi-text-right">
										<a href="<?php echo $resourceRoute ?>" class="bfi-btn" target="_blank"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_VIEWOFFER') ?></a>
								</div>
							</div>
							<!-- end resource details -->
							<div class="bfi-clearfix"></div>
							<!-- end price details -->
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php if ($this->pagination->get('pages.total') > 1) { ?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php } ?>

	<?php }else{?>
	<div class="bfi-noresults">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_NORESULT')?>
	</div>
	<?php } ?>	

<script type="text/javascript">
<!--
	jQuery('#list-view').click(function() {
		jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
		jQuery('#bfi-list').removeClass('bfi-grid-group')
		jQuery('#bfi-list .bfi-item').addClass('bfi-list-group-item')
		jQuery('#bfi-list .bfi-img-container').addClass('bfi-col-sm-3')
		jQuery('#bfi-list .bfi-details-container').addClass('bfi-col-sm-9')

		localStorage.setItem('display', 'list');
	});

	jQuery('#grid-view').click(function() {
		jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
		jQuery('#bfi-list').addClass('bfi-grid-group')
		jQuery('#bfi-list .bfi-item').removeClass('bfi-list-group-item')
		jQuery('#bfi-list .bfi-img-container').removeClass('bfi-col-sm-3')
		jQuery('#bfi-list .bfi-details-container').removeClass('bfi-col-sm-9')
		localStorage.setItem('display', 'grid');
	});
		jQuery('#bfi-list .bfi-item').addClass('bfi-grid-group-item')

	if (localStorage.getItem('display')) {
		if (localStorage.getItem('display') == 'list') {
			jQuery('#list-view').trigger('click');
		} else {
			jQuery('#grid-view').trigger('click');
		}
	} else {
	 if(typeof bfi_variable === 'undefined' || bfi_variable.bfi_defaultdisplay === 'undefined') {
			jQuery('#list-view').trigger('click');
		 } else {
			if (bfi_variable.bfi_defaultdisplay == '1') {
				jQuery('#grid-view').trigger('click');
			} else { 
				jQuery('#list-view').trigger('click');
			}
		}
	}

	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
   };
   jQuery(document).ready(function() {
	  jQuery(".bfi-description").shorten(shortenOption);
   });
//-->
</script>