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
$showdata = $config->get('showdata', 1);

$app = JFactory::getApplication();
$maxItemsView = 3;
$language = $this->language;
$searchid = "";// $this->params['searchid'];
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$merchants = $this->items;

$listsId = array();
$listResourceIds = array();


$activePrice = '';
$activeRating = '';
$activeReviewvalue = '';
$activeOffer = '';

if($listOrder == 'stay') {
	$activePrice = ' active';
}
if($listOrder == 'rating') {
	$activeRating = ' active';
}
if($listOrder == 'reviewvalue') {
	$activeReviewvalue = ' active';
}
if($listOrder == 'offer') {
	$activeOffer = ' active';
}

//-------------------pagina per i l redirect di tutte le risorse 

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = intval($db->loadResult());

$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
  $db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
  $itemIdMerchant = intval($db->loadResult());
}
if($itemId == 0){
	$itemId = $itemIdMerchant;
}

$uriFav = 'index.php?option=com_bookingforconnector&view=resources&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdFav = intval($db->loadResult());
if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);

$img = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$imgError = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantLogo =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";
$merchantLogoError =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
//$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

//$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
//$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'medium');

$offersDefault=Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";

$urisearch  = 'index.php?option=com_bookingforconnector&view=search';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $urisearch .'%' ) .' AND (language='. $db->Quote($language) .'  OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemIdsearch = ($db->getErrorNum())? 0 : intval($db->loadResult());

$formAction = JRoute::_($urisearch);
if ($itemIdsearch<>0){
	$formAction = JRoute::_('index.php?Itemid='.$itemIdsearch.'' );
}
$url=JFactory::getURI()->toString();
$action=$url;

$hidesort = "";
//if(!empty($this->hidesort)) {
//	$hidesort = ' style="display:none;"';
//}

$onlystay =  true;
if(!empty($this->params['onlystay'])){
	$onlystay =  $this->params['onlystay'] === 'false'? false: true;
}
?>
	<div class="com_bookingforconnector-search-menu">
		<form action="<?php echo $action; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
			<fieldset class="filters">
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
				<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
				<input type="hidden" name="limitstart" value="0" />
			</fieldset>
		</form>
		<div class="com_bookingforconnector-results-sort" style="display:none;">
			<span class="com_bookingforconnector-sort-item"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
			<span class="com_bookingforconnector-sort-item<?php echo $activePrice; ?>" rel="stay|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_PRICE'); ?></span>
<!-- 			<span class="com_bookingforconnector-sort-item<?php echo $activeRating; ?>" rel="rating|desc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_RATING'); ?></span> -->
			<span class="com_bookingforconnector-sort-item<?php echo $activeReviewvalue; ?>" rel="reviewvalue|desc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_GUEST_RATING'); ?></span>
			<span class="com_bookingforconnector-sort-item<?php echo $activeOffer; ?>" rel="offer|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_OFFERS'); ?></span>
		</div>
		<div class="com_bookingforconnector-view-changer">
			<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
		</div>
	</div>
	
	<div class="clearfix"></div>
	
	<div class="com_bookingforconnector-search-merchants com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
		<?php 
		$listResourceIds = array(); 
		$listResourceIdsByMerchant = array();
		?>

		<?php foreach ($merchants as $merchant): ?>
		<?php 
			$rating = $merchant->Rating;

			$routeInfoRequest = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&layout=contactspopup&tmpl=component&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));
			$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

			if ($itemIdMerchant<>0)
				$currUriMerchant.='&Itemid='.$itemIdMerchant;
			
			$routeMerchant = JRoute::_($currUriMerchant);
			$routeRating = JRoute::_($currUriMerchant.'&layout=ratings');				
			$routeRatingform = JRoute::_($currUriMerchant.'&layout=rating');				

			$httpsPayment = $merchant->PaymentType;
			
			$counter = 0;
			$merchantLat = $merchant->XGooglePos;
			$merchantLon = $merchant->YGooglePos;
			$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
			$merchantLogo =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";
			$merchantLogoError =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";
			if(!empty($merchant->LogoUrl)){
				$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logomedium');
				$merchantLogoError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'logomedium');
			}
			
			if(!empty($merchant->ImageUrl)){
				$merchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->ImageUrl, 'medium');
			}

		?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" >
				<div class="com_bookingforconnector-search-merchant com_bookingforconnector-item  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
					<div class="mrcgroup" id="bfcmerchantgroup<?php echo $merchant->MerchantId; ?>"><span class="bfcmerchantgroup"></span></div>
					<div class="com_bookingforconnector-item-details  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
							<div id="com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" class="carousel" data-ride="carousel" data-interval="false">
								<div class="carousel-inner" role="listbox">
									<div class="item active"><img src="<?php echo $merchantImageUrl; ?>"></div>
								</div>
								<?php if($isportal): ?>
									<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-grid" href="<?php echo $routeMerchant ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogo; ?>" id="com_bookingforconnector-logo-grid-<?php echo $merchant->MerchantId?>" /></div></a>
								<?php endif; ?>
								<a class="left carousel-control" href="#com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" role="button" data-slide="prev">
									<i class="fa fa-chevron-left"></i>
								</a>
								<a class="right carousel-control" href="#com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" role="button" data-slide="next">
									<i class="fa fa-chevron-right"></i>
								</a>
							</div>
						</div>
						<div class="com_bookingforconnector-search-merchant-details com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div class="com_bookingforconnector-search-merchant-name com_bookingforconnector-item-primary-name">
								<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor namelist" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>"><?php echo  $merchant->Name ?></a> 
								<span class="com_bookingforconnector-search-merchant-rating com_bookingforconnector-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
							<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
								<div class="ratinggrid">
									<?php if ($merchant->reviewCount>0):?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-search-merchant-review-value com_bookingforconnector-item-review-value" href="<?php echo $routeRating ?>" id="ratingAnchorvalue<?php echo $merchant->MerchantId?>" ><?php echo $merchant->reviewValue?></a>
										<a class="com_bookingforconnector-search-merchant-review-count com_bookingforconnector-item-review-count" href="<?php echo $routeRating ?>" id="ratingAnchorcount<?php echo $merchant->MerchantId?>" ><?php echo $merchant->reviewCount?></a>
									</div>
									<?php else: ?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-leaverating " href="<?php echo $routeRatingform ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_LEAVE_COMMENT') ?></a>
									</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							</div>
							<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor namegrid" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>"><?php echo  $merchant->Name ?></a> 
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-address">
								<?php if ($showMerchantMap):?>
								<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><span id="address<?php echo $merchant->MerchantId?>"></span></a>
								<?php endif; ?>
							</div>
							<div class="ratinglist">
								<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
									<?php if ($merchant->reviewCount>0):?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-search-merchant-review-value com_bookingforconnector-item-review-value" href="<?php echo $routeRating ?>" id="ratingAnchorvalue<?php echo $merchant->MerchantId?>" ><?php echo $merchant->reviewValue?></a>
										<a class="com_bookingforconnector-search-merchant-review-count com_bookingforconnector-item-review-count" href="<?php echo $routeRating ?>" id="ratingAnchorcount<?php echo $merchant->MerchantId?>" ><?php echo $merchant->reviewCount?></a>
									</div>
									<?php else: ?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-leaverating " href="<?php echo $routeRatingform ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_LEAVE_COMMENT') ?></a>
									</div>
									<?php endif; ?>
								<?php endif; ?>&nbsp;
							</div>
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-phone-inforequest"> 
								<span class="com_bookingforconnector_phone">
								<a  href="javascript:void(0);" 
									onclick="getData(urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes( $merchant->Name) ?>','PhoneView' )"  id="phone<?php echo $merchant->MerchantId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a>
								</span> - 					
								<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
							</div>
						</div>

						<?php if($isportal): ?>
							<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
								<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list" href="<?php echo $routeMerchant ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogo; ?>" id="com_bookingforconnector-logo-list-<?php echo $merchant->MerchantId?>" /></a>
							</div>
						<?php endif; ?>
					</div>

			<!-- resource list -->
				<?php 
					$count = 0; 
					$discount = 0;
					$maxviewExceeded = FALSE;
					$listResourceIdsByMerchant = array();

				
				//$resource->SimpleDiscountIds  = implode(',',$tmpDiscountIds);

				?>
				<?php foreach($merchant->Resources as $resource) : ?>  
					<?php
					$resource->SimpleDiscountIds = "";
					$resource->DiscountIds = json_decode($resource->DiscountIds);
					if(is_array($resource->DiscountIds) && count($resource->DiscountIds)>0){
//						$tmpDiscountIds = array_unique(array_map(function ($i) { return $i->VariationPlanId; }, $resource->DiscountIds ));
						$resource->SimpleDiscountIds  = implode(',',$resource->DiscountIds);
					}		
						if($count < $maxItemsView){
							$listResourceIds[]= $resource->ResourceId; 
						}else{
							$listResourceIdsByMerchant[]= $resource->ResourceId; 
						}
					?>
					<?php if($count == $maxItemsView):?>
					<?php $maxviewExceeded = TRUE; ?>	
					<div id="showallresource<?php echo $merchant->MerchantId?>" style="display:none;" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
					<?php endif; ?>
					<div class="com_bookingforconnector-search-resource-details com_bookingforconnector-item-secondary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="padding-top: 10px !important;padding-bottom: 10px !important;">
						<?php 
							$resourceName = BFCHelper::getLanguage($resource->ResName, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
//							$resourceName = trim(str_replace(" - ".$resource->RateplanName,"",$resourceName)); 
							$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
							if ($itemId<>0){
								$currUriresource.='&Itemid='.$itemId;
							}
							$resourceRoute = JRoute::_($currUriresource);
							$bookingType = $resource->BookingType;
							$classofferdisplay = "";
							if (($resource->Price < $resource->TotalPrice) || $resource->IsOffer){
								$classofferdisplay = "com_bookingforconnector_highlight";
							}
							if (!empty($resource->RateplanId)){
								$resourceRoute .= "?pricetype=" . $resource->RateplanId;
							}
						?>
						<div class="com_bookingforconnector-search-resource-details-name com_bookingforconnector-item-secondary-name" style="padding-left:10px;">
							<a href="<?php echo $resourceRoute?>"><?php echo $resourceName; ?></a>
							<?php if ($resource->PercentVariation<0): ?><div class="specialoffer variationlabel" rel="<?php echo  $resource->SimpleDiscountIds ?>"  rel1="<?php echo  $resource->ResourceId ?>" ><?php echo $resource->PercentVariation ?>% <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?> <i class="fa fa-angle-down" aria-hidden="true"></i></div><?php endif; ?>
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 divoffers" id="divoffers<?php echo  $resource->ResourceId ?>" style="display:none ;">
								<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
								<span class="sr-only">Loading...</span>
						</div>
						<div class="clearfix"></div>
						
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection" >
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-1 secondarysectionitem">	 
									<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes">
										<i class="fa fa-user"></i>
										<?php if ($resource->MinPaxes == $resource->MaxPaxes):?>
											<?php echo  $resource->MaxPaxes ?>
										<?php else: ?>
											<?php echo  $resource->MinPaxes ?>-<?php echo  $resource->MaxPaxes ?>
										<?php endif; ?>
									</div>
									<?php if ($onlystay): ?>
										<div class="com_bookingforconnector-search-resource-details-availability com_bookingforconnector-item-secondary-availability">
										<?php if ($resource->Availability < 4): ?>
										  <?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LESSAVAIL'),$resource->Availability) ?>
										<?php else: ?>
										  <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_AVAILABLE')?>
										<?php endif; ?>
										</div>

										<?php if (!$resource->IsBase): ?>
											<div class="com_bookingforconnector_res_rateplanname">
												<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TREATMENT')?>:<br />
												<span><?php echo $resource->RateplanName ?></span>
											</div>
										<?php endif; ?>

									<?php endif; ?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-2 secondarysectionitem">
									<?php if ($onlystay): ?>
										<?php if ($resource->Price > 0): ?>
											<div class="com_bookingforconnector-search-grouped-resource-details-price com_bookingforconnector-item-secondary-price">
												<span class="com_bookingforconnector-gray-highlight"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),$resource->Days) ?></span>
												 <div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
													<?php if ($resource->Price < $resource->TotalPrice): ?>
													<?php 
													  $current_discount = $resource->PercentVariation;
													  $discount = $current_discount < $discount ? $current_discount : $discount;
													?>
													<span class="com_bookingforconnector_strikethrough"><span class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount">&euro; <?php echo number_format($resource->TotalPrice,2, ',', '.')  ?></span></span>
													<?php endif; ?>
													<span class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total"><?php echo number_format($resource->Price,2, ',', '.') ?></span>
												</div>
											</div>
										<?php else: ?>
											<div class="com_bookingforconnector-search-resource-details-price com_bookingforconnector-item-secondary-price">
												<span class="com_bookingforconnector-gray-highlight" id="totaldays<?php echo $resource->ResourceId?>"></span>
												<div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
													<span id="resourcestaytotal<?php echo $resource->ResourceId?>" class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total"><i class="fa fa-spinner fa-spin"></i></span>
													<span class="com_bookingforconnector_strikethrough"><span id="resourcestaydiscount<?php echo $resource->ResourceId?>"  class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount"></span></span>
												</div>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 com_bookingforconnector-item-secondary-section-3 secondarysectionitem">
									<?php if ($resource->Price > 0): ?>
											<?php if (isset($bookingType) && $bookingType>0):?>
												<a href="<?php echo $resourceRoute ?>" class=" com_bookingforconnector-item-secondary-more"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_HTTPS')?></a>
											<?php else: ?>
												<a href="<?php echo $resourceRoute ?>" class=" com_bookingforconnector-item-secondary-more"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
											<?php endif; ?>
									<?php else: ?>
											<a href="<?php echo $resourceRoute ?>" class=" com_bookingforconnector-item-secondary-more"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
									<?php endif; ?>
								</div>
						</div>



					</div>
					<?php $count++; ?>
				<?php endforeach; ?>
				<?php if($maxviewExceeded == TRUE) : ?>
					</div>
					<div class="com_bookingforconnector-search-resource-details-showmax <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12"><a onclick="showallresource('#showallresource<?php echo $merchant->MerchantId?>',this,'<?php echo implode(',',$listResourceIdsByMerchant) ?>')" style="padding-left:10px;"> <i class="icon-plus "></i><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_SHOWALL') ?></a></div>

				<?php endif; ?>	
					<div class="discount-box" style="display:<?php if($discount < 0) { ?>block<?php }else{ ?>none<?php } ?>;">
						<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_DISCOUNT_BOX_TEXT'), number_format($discount, 1)); ?>
					</div>
				
				</div>
				<div class="clearfix"><br /></div>
			</div>
		<?php $listsId[]= $merchant->MerchantId; ?>
	<?php endforeach; ?>
	</div>
<script type="text/javascript">
<!--

jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2');
	localStorage.setItem('display', 'list');
});

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	localStorage.setItem('display', 'grid');
});

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfc_display === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfc_display == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}

var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";
var listToCheck = "<?php echo implode(",", $listsId) ?>";
//var imgPath = "<?php //echo $merchantLogoPath ?>";
//var imgPathError = "<?php //echo $merchantLogoPathError ?>";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'tag24') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'tag24') ?>";
var cultureCodeMG = '<?php echo $language ?>';
var defaultcultureCodeMG = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';

var strRatingNoResult = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_NO_RESULT')?>";
var strRatingBased = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_BASED')?>";
var strRatingValuation = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_VALUATION')?>";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};

var mg = [];

var loaded=false;

function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var queryMG = "task=getMerchantGroups";
//		var urlgetMG = updateQueryStringParameter(urlCheck,"task","getMerchantGroups");

//		jQuery.getJSON(urlgetMG, function(data) {
		jQuery.post(urlCheck, queryMG, function(data) {
				if(data!=null)
				jQuery.each(data, function(key, val) {
					if (val.ImageUrl!= null && val.ImageUrl!= '') {
						var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
						var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
						/*--------getName----*/
						var $name = getXmlLanguage(val.Name,cultureCodeMG,defaultcultureCodeMG);
						/*--------getName----*/
						mg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
					} else {
						if (val.IconSrc != null && val.IconSrc != '') {
							mg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
						}
					}
				});	
				getlist();
		},'json');
	}
}

function getResourceslist(listResourceIdsToCheck,loadMerchantlist){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "resourcesId=" + listResourceIdsToCheck + "&language=<?php echo $language ?>";
		query +="&task=GetResourcesByIds";

	if(listResourceIdsToCheck!=''){
				if(loadMerchantlist){
					getlist();
				}
//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
				jQuery.each(data || [], function(key, val) {
					//price
					jQuery("#resourcestaytotal"+val.Resource.ResourceId).html("&nbsp; ");
					if (val.Resource.RatePlanStay != null && val.Resource.RatePlanStay != '' && val.Resource.RatePlanStay.SuggestedStay!= null) {
						var st = val.Resource.RatePlanStay.SuggestedStay;
						var TotalPrice = parseFloat(st.TotalPrice);
						var DiscountedPrice = parseFloat(st.DiscountedPrice);
						if(TotalPrice>0){
							var currnt = "<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),'') ?>"
							jQuery("#totaldays"+val.Resource.ResourceId).html(currnt.replace("0", st.Days));
							if(DiscountedPrice< TotalPrice){
								jQuery("#resourcestaydiscount"+val.Resource.ResourceId).html("&euro; " +number_format(TotalPrice, 2, '.', ''));
							}
							jQuery("#resourcestaytotal"+val.Resource.ResourceId).html("&euro; " + number_format(DiscountedPrice, 2, '.', ''));
							if(st.IsOffer){
								jQuery(".container"+val.Resource.ResourceId).addClass("com_bookingforconnector_highlight");
							}
						}
					}
			});	
		},'json');

	}
}

function getlist(){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "merchantsId=" + listToCheck + "&language=<?php echo $language ?>&task=GetMerchantsByIds";
	if(listToCheck!='')
	
	var imgPath = "<?php echo $merchantImagePath ?>";
	var imgPathError = "<?php echo $merchantImagePathError ?>";
	
//	var logoPath = "<?php //echo $merchantLogoPath ?>";
//	var logoPathError = "<?php //echo $merchantLogoPathError ?>";
//	var logoPathresized =  imgPath.substring(0,logoPath.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";
//	logoPath = logoPath.replace(logoPathresized , "" );

//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
				jQuery("#descr"+val.MerchantId).removeClass("com_bookingforconnector_loading");

            if (val.ImageData!= null && val.ImageData!= '') {
					var imgSliderData = '';
					var ImageData = val.ImageData.split(',');
					var start = 0;
                jQuery.each(ImageData,function(index){
                  // new system with preresized images
					  imgLogo = imgPath.replace("[img]", jQuery.trim(ImageData[index]));

					  // old system with resized images on the fly
					  imgLogoError = imgPathError.replace("[img]", ImageData[index]);
					  if(start == 0) {
					    imgSliderData = imgSliderData + '<div class="item active"><img src="'+imgLogo+'"></div>';					  
					  }
					  else {
					    imgSliderData = imgSliderData + '<div class="item"><img src="'+imgLogo+'"></div>';
				     }
				     start++;
                });
                jQuery('#com_bookingforconnector-search-merchant-carousel'+val.MerchantId).carousel("pause").removeData();
                jQuery('#com_bookingforconnector-search-merchant-carousel'+val.MerchantId+' .carousel-inner').html(imgSliderData);
                jQuery('#com_bookingforconnector-search-merchant-carousel'+val.MerchantId).carousel('pause');
				}
				
				if (val.AddressData != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					xmlDoc = jQuery.parseXML(val.AddressData);
					if(xmlDoc!=null){
						$xml = jQuery(xmlDoc);
						$indirizzo = $xml.find("indirizzo:first").text();
						$cap = $xml.find("cap:first").text();
						$comune = $xml.find("comune:first").text();
						$provincia = $xml.find("provincia:first").text();
					}else{
						$indirizzo = val.AddressData.Address;
						$cap = val.AddressData.ZipCode;
						$comune = val.AddressData.CityName;
						$provincia = val.AddressData.RegionName;
					}
					merchAddress = strAddress.replace("[indirizzo]",$indirizzo);
					merchAddress = merchAddress.replace("[cap]",$cap);
					merchAddress = merchAddress.replace("[comune]",$comune);
					merchAddress = merchAddress.replace("[provincia]",$provincia);
					jQuery("#address"+val.MerchantId).append('<i class="fa fa-map-marker" aria-hidden="true"></i> ' + merchAddress);
				}
				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '<span class="bfcmerchantgroup">';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					$htmlmg += '</span>';
					jQuery("#bfcmerchantgroup"+val.MerchantId).html($htmlmg);
				}
				

				jQuery("#container"+val.MerchantId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.MerchantId ).attr("href");
					}
				});

//				if (val.RatingsContext!= null && (val.RatingsContext == '1' || val.RatingsContext == '3')){
//					$htmlAvg = '';
//					if (val.Avg != null && val.Avg != '' ) {
//						jQuery("#ratingAnchorvalue"+val.MerchantId).html(number_format(val.Avg.Average, 1, '.', ''));
//						jQuery("#ratingAnchorcount"+val.MerchantId).html(strRatingBased.replace("%s", val.Avg.Count));
//					}
//				}else{
//					jQuery("#ratingAnchorvalue"+val.MerchantId).parent().hide();		
//					jQuery("#ratingAnchorcount"+val.MerchantId).parent().hide();				
//				}
//				jQuery('span[id^="resourcestaytotal"]:visible:has(i)').html("&nbsp; ");

		});	
		jQuery('[data-toggle="tooltip"]').tooltip(); 
	
	},'json');
}

function getDiscountsAjaxInformations(discountIds,obj, fn){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {
			$html = '';
			jQuery.each(data || [], function(key, val) {
				var name = val.Name;
				var descr = val.Description;
				name = nl2br(jQuery("<p>" + name + "</p>").text());
				$html += '<p class="title">' + name + '</p>';
				descr = nl2br(jQuery("<p>" + descr + "</p>").text());
				$html += '<p class="description ">' + descr + '</p>';
			});
			offersLoaded[discountIds] = $html;
			fn(obj,$html);
			//jQuery(obj).html($html);
	},'json');

}
function getRateplanAjaxInformations(rateplanId){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "rateplanId=" + rateplanId + "&language=<?php echo $language ?>&task=getRateplanDetails";
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {

				var name = getXmlLanguage(data.Name,cultureCode,defaultcultureCode);;
				name = nl2br(jQuery("<p>" + name + "</p>").text());
				jQuery("#divrateplanTitle"+rateplanId).html(name);

				var descr = getXmlLanguage(data.Description,cultureCode,defaultcultureCode);;
				descr = nl2br(jQuery("<p>" + descr + "</p>").text());
				jQuery("#divrateplanDescr"+rateplanId).html(descr);
				jQuery("#divrateplanDescr"+rateplanId).removeClass("com_bookingforconnector_loading");
	},'json');

}

var offersLoaded = []
var rateplansLoaded = []
jQuery(document).ready(function() {
	
	getAjaxInformations();

	jQuery('.mod_bookingformaps-static').click(function() {
		jQuery( "#mod_bookingformaps-popup" ).dialog({
			open: function( event, ui ) {
				openGoogleMapSearch();
			},
			height: 500,
			width: 800,
		});
	});
	
	jQuery('.com_bookingforconnector-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
	  jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
	  
	  if(jQuery('#searchformfilter').length){
		  jQuery('#searchformfilter').submit();
	  }else{
		  jQuery('#bookingforsearchFilterForm').submit();
	  }
	});
	
	jQuery('#bookingforsearchFilterForm').ajaxForm({
			target:     '#bfcmerchantlist',
			replaceTarget: true, 
			url:        '<?php echo $formAction; ?>'+'', 
			data: 	{ format: 'raw', tmpl: 'component',limitstart:'0' },
			beforeSerialize:function() {
				try
				{
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
			},
			beforeSend: function() {
				jQuery('#bfcmerchantlist').block();
			},
			success: showResponse,
			error: showError
	});
	
	jQuery(".variationlabel").click(function(){
		var show = function(resourceId, text){
					jQuery("#divoffers"+resourceId).empty();
					//jQuery("#divoffers"+resourceId).removeClass("com_bookingforconnector_loading");
					jQuery("#divoffers"+resourceId).html(text);
		};
		var discountIds = jQuery(this).attr('rel'); 
		var resourceId = jQuery(this).attr('rel1'); 
//		jQuery("#divoffers"+resourceId).slideToggle( "slow" );
		if(jQuery("#divoffers"+resourceId).is(":visible")){
			jQuery("#divoffers"+resourceId).slideUp("slow");
			jQuery(this).children().toggleClass("fa-angle-up fa-angle-down");
		  } else {
			jQuery("#divoffers"+resourceId).slideDown("slow");
			jQuery(this).children().toggleClass("fa-angle-up fa-angle-down");
		  }

		//if (jQuery.inArray(discountIds,offersLoaded)===-1)
		if(!offersLoaded.hasOwnProperty(discountIds))
		{
			getDiscountsAjaxInformations(discountIds,resourceId,show);
			//offersLoaded.push(discountIds);
		}else{
			show(resourceId,offersLoaded[discountIds]);
			//jQuery("#divoffers"+resourceId).html(offersLoaded[discountIds]);
		}
	});

	jQuery(".rateplanslabel").click(function(){
				var rateplanId = jQuery(this).attr('rel'); 
				if (jQuery.inArray(rateplanId,rateplansLoaded)===-1)
				{
					getRateplanAjaxInformations(rateplanId);
					rateplansLoaded.push(rateplanId);
				}
				jQuery("#divrateplan"+rateplanId).slideToggle( "slow" );
			});

});

function showallresource(who,elm,listid){
	getResourceslist(listid,false);
	jQuery(who).show();
	jQuery(elm).hide();

}
//-->
</script>
