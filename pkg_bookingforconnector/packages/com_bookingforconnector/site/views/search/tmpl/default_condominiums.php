<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$rating_text = array('merchants_reviews_text_value_0' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_0'),
						'merchants_reviews_text_value_1' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_1'),   
						'merchants_reviews_text_value_2' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_2'),
						'merchants_reviews_text_value_3' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_3'),
						'merchants_reviews_text_value_4' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_4'),
						'merchants_reviews_text_value_5' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_5'),  
						'merchants_reviews_text_value_6' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_6'),
						'merchants_reviews_text_value_7' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_7'),  
						'merchants_reviews_text_value_8' =>JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_8'), 
						'merchants_reviews_text_value_9' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_9'),  
						'merchants_reviews_text_value_10' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_10'),                                 
					);
$currencyclass = bfi_get_currentCurrency();
$listNameAnalytics = $this->listNameAnalytics;
$fromsearchparam = "&fromsearch=1&lna=".$listNameAnalytics;

$language = $this->language;
$total = $this->pagination->total;
$searchid =  $this->params['searchid'];
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$merchants = $this->items;

$listsId = array();
$listResourceIds = array();

$currSorting=$listOrder . "|" . $listDirn;

//-------------------pagina per il redirect di tutte le risorse 

//$db   = JFactory::getDBO();
//$uri  = 'index.php?option=com_bookingforconnector&view=condominium';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') LIMIT 1' );
////$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemId = intval($db->loadResult());
////------------------- pagina per i l redirect di tutte le risorse 
//
//$uriresource  = 'index.php?option=com_bookingforconnector&view=resource';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriresource ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') LIMIT 1' );
////$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdresource = intval($db->loadResult());
//
////-------------------pagina per il redirect di tutti i merchant
//$itemIdMerchant=0;
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//if($isportal){
//  $db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//  $itemIdMerchant = intval($db->loadResult());
//}
//if($itemId == 0){
//	$itemId = $itemIdMerchant;
//}

$uri = COM_BOOKINGFORCONNECTOR_URICONDOMINIUM;
$uriresource = COM_BOOKINGFORCONNECTOR_URIRESOURCE;
$uriMerchant  = COM_BOOKINGFORCONNECTOR_URIMERCHANTDETAILS;

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$onlystay = true ;
$currParam = BFCHelper::getSearchParamsSession();
if(isset($currParam) && isset($currParam['onlystay'])){
		$onlystay =  ($currParam['onlystay'] === 'false' || $currParam['onlystay'] === 0)? false: true;
}

$url=JFactory::getURI()->toString();
$formAction=$url;

$totalResult = $total;

$checkin = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
$checkout = BFCHelper::getStayParam('checkout', new DateTime('UTC'));
$checkin = new JDate($checkin->format('Y-m-d')); 
$checkout = new JDate($checkout->format('Y-m-d')); 
$checkinstr = $checkin->format("d") . " " . $checkin->format("M") . ' ' . $checkin->format("Y") ;
$checkoutstr = $checkout->format("d") . " " . $checkout->format("M") . ' ' . $checkout->format("Y") ;
$totPerson = (isset($currParam)  && isset($currParam['paxes']))? $currParam['paxes']:0 ;

?>
<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-xs-9 ">
			<div class="bfi-search-title">
				<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_TITLE_DEFAULT') ,$totalResult ) ?>
			</div>
			<div class="bfi-search-title-sub">
				<?php echo sprintf( JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_TITLE_FROM') ,$checkinstr,$checkoutstr ) ?>
			</div>
		</div>	
	<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
		<div class="bfi-col-xs-3 ">
			<div class="bfi-search-view-maps ">
			<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_MAPVIEW') ?></span>
			</div>	
		</div>	
	<?php } ?>
	</div>	
	<div class="bfi-search-menu">
		<form action="<?php echo $formAction; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
				<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
				<input type="hidden" name="limitstart" value="0" />
		</form>
		<div class="bfi-results-sort">
			<span class="bfi-sort-item"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
			<span class="bfi-sort-item <?php echo $currSorting=="price|asc" ? "bfi-sort-item-active": "" ; ?>" rel="price|asc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_PRICE'); ?></span>
			<!-- <span class="bfi-sort-item <?php echo $currSorting=="rating|desc" ? "bfi-sort-item-active": "" ; ?>" rel="rating|desc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_GUEST_RATING'); ?></span> -->
			<span class="bfi-sort-item <?php echo $currSorting=="offer|asc" ? "bfi-sort-item-active": "" ; ?>" rel="offer|asc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_OFFERS'); ?></span> 
		</div>
		<div class="bfi-view-changer">
			<div class="bfi-view-changer-selected"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div class="bfi-view-changer-content">
				<div id="list-view"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
				<div id="grid-view" class="bfi-view-changer-grid"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
			</div>
		</div>
	</div>
	<div class="bfi-clearfix"></div>

<div id="bfi-list" class="bfi-row bfi-list">
<?php 
foreach ($merchants as $currKey => $merchant){ 


	$currName = BFCHelper::getLanguage($merchant->GrpName, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$merchantName = $merchant->MrcName;

	$isCondominium = (!empty( $merchant->CondominiumId ) && ($merchant->CondominiumId != $merchant->ResourceId));

	$currUri = $uri. '&resourceId=' . $merchant->CondominiumId. ':' . BFCHelper::getSlug($merchant->GrpName);

//	if ($itemId<>0)
//		$currUri.='&Itemid='.$itemId;

	$currUri .= $fromsearchparam;
	$routeCondominium = JRoute::_($currUri);
	
	$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchantName);
//	if ($itemIdMerchant<>0)
//		$currUriMerchant.='&Itemid='.$itemIdMerchant;
	
	$routeMerchant = JRoute::_($currUriMerchant);
	$routeRating = JRoute::_($currUriMerchant.'&layout=ratings');				
	$routeInfoRequest = JRoute::_($currUriMerchant.'&layout=contactspopup&tmpl=component');			

	$httpsPayment = $merchant->PaymentType;
		
	$counter = 0;
	$merchantLat = $merchant->ResLat;
	$merchantLon = $merchant->ResLng;
	$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
	
	$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

	if(!empty($merchant->GrpImageUrl)){
		$merchantImageUrl = BFCHelper::getImageUrlResized('condominium',$merchant->GrpImageUrl, 'medium');
	}

	$merchant->SimpleDiscountIds = "";
	$merchant->DiscountIds = json_decode($merchant->DiscountIds);
	if(is_array($merchant->DiscountIds) && count($merchant->DiscountIds)>0){
		$merchant->SimpleDiscountIds  = implode(',',$merchant->DiscountIds);
	}		

	$resourceName = BFCHelper::getLanguage($merchant->ResName, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$currUriresource = $uriresource.'&resourceId=' . $merchant->ResourceId . ':' . BFCHelper::getSlug($merchant->ResName);
//	if ($itemIdresource<>0){
//		$currUriresource.='&Itemid='.$itemIdresource;
//	}
	$currUriresource .= $fromsearchparam;
	if (!empty($merchant->RateplanId)){
		$currUriresource .= "&pricetype=" . $merchant->RateplanId;
	}
	
	$resourceRoute = JRoute::_($currUriresource);

	$bookingType = $merchant->BookingType;
	$IsBookable = $merchant->IsBookable;
	$btnText = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_REQ');
	$btnClass = "bfi-alternative";
	if ($IsBookable){
		$btnText = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_HTTPS');
		$btnClass = "";
	}
	$classofferdisplay = "";
	if (($merchant->Price < $merchant->TotalPrice) || $merchant->IsOffer){
		$classofferdisplay = "bfi-highlight";
	}
	
	if(!$isCondominium){
		$currName = $resourceName;
		$routeCondominium = $resourceRoute;
		if(!empty($merchant->ImageUrl)){
			$merchantImageUrl = BFCHelper::getImageUrlResized('resources',$merchant->ImageUrl, 'medium');
		}
	}

	$hasSuperiorMrc = !empty($merchant->MrcRatingSubValue);
	$ratingMrc = (int)$merchant->MrcRating;
	if ($ratingMrc>9 )
	{
		$ratingMrc = $ratingMrc/10;
		$hasSuperiorMrc = ($merchant->MrcRating%10)>0;
	} 

	$resourceDataTypeTrack =  ($isCondominium)?"Resource Group":"Resource";
	$resourceNameTrack =  BFCHelper::string_sanitize($currName);
	$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
	$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MrcCategoryName);

?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $routeCondominium ?>" style='background: url("<?php echo $merchantImageUrl; ?>") center 25%;background-size: cover;' target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><img src="<?php echo $merchantImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $routeCondominium ?>" id="nameAnchor<?php echo $merchant->CondominiumId?>" target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $currName ?></a> 
							<?php if($isportal) { ?>
								- <a href="<?php echo $routeMerchant?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $merchantName; ?></a>
								<span class="bfi-item-rating">
									<?php for($i = 0; $i < $ratingMrc; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>
									<?php if ($hasSuperiorMrc) { ?>
										&nbsp;S
									<?php } ?>									
								</span>
							<?php } ?>
						</div>
						<div class="bfi-item-address">
							<?php if ($showMerchantMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->ResourceId?>)"><span id="address<?php echo $merchant->CondominiumId?>"></span></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $merchant->CondominiumId; ?>"></div>
					</div>
					<?php if(false) { ?>
						<div class="bfi-col-sm-3 bfi-text-right">
							<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)){?>
									<div class="bfi-avg">
									<?php if ($merchant->MrcAVGCount>0){
										$totalInt = BFCHelper::convertTotal(number_format((float)$merchant->MrcAVG, 1, '.', ''));

										?>
										<a class="bfi-avg-value eectrack" href="<?php echo $routeRating ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt] . " " . number_format((float)$merchant->MrcAVG, 1, '.', '') ?></a><br />
										<a class="bfi-avg-count eectrack" href="<?php echo $routeRating ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_TOTAL'),$merchant->MrcAVGCount) ?></a>
									<?php } ?>
									</div>
							<?php } ?>
						</div>
					<?php } ?>
					
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->

				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-6">
						<?php if ($merchant->MaxPaxes>0){?>
							<div class="bfi-icon-paxes">
								<i class="fa fa-user"></i> 
								<?php if ($merchant->MaxPaxes==2){?>
								<i class="fa fa-user"></i> 
								<?php }?>
								<?php if ($merchant->MaxPaxes>2){?>
									<?php echo ($merchant->MinPaxes != $merchant->MaxPaxes)? $merchant->MinPaxes . "-" : "" ?><?php echo  $merchant->MaxPaxes ?>
								<?php }?>
							</div>
						<?php } ?>
						<?php if($isCondominium) { ?>
							<a href="<?php echo $resourceRoute?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $resourceName; ?></a>
						<?php } ?>
						
					</div>
					<div class="bfi-col-sm-3 ">
						<?php if (!$merchant->IsCatalog && $onlystay ){ ?>
							<div class="bfi-availability">
							<?php if ($merchant->Availability > 0 && $merchant->Availability < 2){ ?>
							  <span class="bfi-availability-low"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LESSAVAIL') ,$merchant->Availability) ?></span>
							<?php } ?>
							</div>
						<?php } ?>
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
						<?php if (!$merchant->IsCatalog && $onlystay ){ 
														
							if($merchant->IncludedMeals >-1){
								switch ($merchant->IncludedMeals) {
								    case bfi_Meal::Breakfast:
											echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_BREAKFAST') ;
										break;
								    case bfi_Meal::BreakfastLunch:
									case bfi_Meal::BreakfastDinner:
									case bfi_Meal::LunchDinner :
											echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_HALFBOARD') ;
										break;
								    case bfi_Meal::BreakfastLunchDinner:
											echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_FULLBOARD') ;
										break;
								    case bfi_Meal::AllInclusive:
											echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_ALLINCLUSIVE') ;
										break;
								        
								}
							}
						} else {?>
							<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON') ?></a>
						<?php } ?>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
																<!-- end resource details -->

				<?php if (!$merchant->IsCatalog && $onlystay && !empty($merchant->AvailabilityDate) ){ ?>
				<!-- price details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-4 bfi-text-right ">
					<?php if ($merchant->MaxPaxes>0){?>
					<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_PRICEPERSON') ,$totPerson) ?>
					<?php } ?>					
					</div>
					<div class="bfi-col-sm-5 bfi-text-right ">
							<div class="bfi-gray-highlight">
							<?php 
								$currCheckIn = DateTime::createFromFormat('Y-m-d\TH:i:s',$merchant->AvailabilityDate,new DateTimeZone('UTC'));
								$currCheckOut = DateTime::createFromFormat('Y-m-d\TH:i:s',$merchant->CheckOutDate,new DateTimeZone('UTC'));
								$currDiff = $currCheckOut->diff($currCheckIn);
								$hours = $currDiff->h;
								$minutes = $currDiff->i;

								switch ($merchant->AvailabilityType) {
									case 0:
										echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR') ;
										echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS') ,$merchant->Days);
										break;
									case 1:
										echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR') ;
										echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_NIGHTS') ,$merchant->Days);
										break;
									case 2:
										echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR') ;
										if($hours >0){
											echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_HOURS') ,$hours);
										}
										if($minutes >0){
											echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_MINUTES') ,$minutes);
										}
										break;
									case 3:
										echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_FROM') ;
										//sospeso momentaneamente
//										echo __('Total for', 'bfi');
//										if($hours >0){
//											echo sprintf(__('%d hour/s' ,'bfi'),$hours);
//										}
//										if($minutes >0){
//											echo sprintf(__('%d minute/s' ,'bfi'),$minutes);
//										}
										break;
								}
							?>
							</div>
							<?php if ($merchant->Price < $merchant->TotalPrice){ ?>
							<span class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?> bfi-cursor" rel="<?php echo $merchant->SimpleDiscountIds ?>"><?php echo number_format($merchant->TotalPrice,2, ',', '.')  ?><span class="bfi-no-line-through">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></span></span>
							<?php } ?>
							<span class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>  <?php echo ($merchant->Price < $merchant->TotalPrice)?"bfi-red":"" ?>" ><?php echo BFCHelper::priceFormat($merchant->Price,2, ',', '.') ?></span>
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
						<?php if ($merchant->Price > 0){ ?>
								<a href="<?php echo $routeCondominium ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $btnText ?></a>
						<?php }else{ ?>
								<a href="<?php echo $routeCondominium ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON') ?></a>
						<?php } ?>
					</div>
				</div>
				<div class="bfi-clearfix"></div>
				<!-- end price details -->
				<?php } ?>
			</div>
			<div class="bfi-discount-box" style="display:<?php echo ($merchant->PercentVariation < 0)?"block":"none"; ?>;">
				<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_DISCOUNT_BOX_TEXT'), number_format($merchant->PercentVariation, 1)); ?>
			</div>
		</div>
	</div>
		
<?php 

		if($isCondominium){
			$listsId[]= $merchant->CondominiumId; 			
		}else{
			$listResourceIds[]= $merchant->ResourceId; 
		}
		
	}
?>
</div>
</div>

<script type="text/javascript">
<!--

var listToCheck = "<?php echo implode(",", $listsId) ?>";
var listToCheckResources = "<?php echo implode(",", $listResourceIds) ?>";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";

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
		var queryMG = "task=getResourceGroups";
		jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(JSON.parse(data) || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name,bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);
							/*--------getName----*/
							mg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
						} else {
							if (val.IconSrc != null && val.IconSrc != '') {
								mg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
							}
						}
					});	
				}
				getlist();
		},'json');
	}
}


function getlist(){
	if(listToCheck!=''){
		var query = "ids=" + listToCheck + "&language=<?php echo $language ?>&task=GetCondominiumsByIds";
		jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
                    var name = bookingfor.getXmlLanguage(val.Name, bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);

				if (val.Address != null && val.Address != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					$indirizzo = val.Address;
					$cap = val.ZipCode;
					$comune = val.CityName;
					$provincia = val.RegionName;

					merchAddress = strAddress.replace("[indirizzo]", $indirizzo);
					merchAddress = merchAddress.replace("[cap]", $cap);
					merchAddress = merchAddress.replace("[comune]", $comune);
					merchAddress = merchAddress.replace("[provincia]", $provincia);
					jQuery("#address"+val.CondominiumId).append(merchAddress);
				}
				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.CondominiumId).html($htmlmg);
				}
				
				jQuery("#container"+val.CondominiumId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.CondominiumId ).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		},'json');
	}


	if(listToCheckResources!=''){
		var query = "resourcesId=" + listToCheckResources + "&language=<?php echo $language ?>&task=GetResourcesByIds";
		

		jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
	
				var $indirizzo = "";
				var $cap = "";
				var $comune = "";
				var $provincia = "";
				
				$indirizzo = val.Resource.AddressData;
				$cap = val.Resource.ZipCode;
				$comune = val.Resource.CityName;
				$provincia = val.Resource.RegionName;

				addressData = strAddress.replace("[indirizzo]",$indirizzo);
				addressData = addressData.replace("[cap]",$cap);
				addressData = addressData.replace("[comune]",$comune);
				addressData = addressData.replace("[provincia]",$provincia);
				jQuery("#address"+val.Resource.ResourceId).html(addressData);

				if (val.Resource.TagsIdList!= null && val.Resource.TagsIdList != '')
				{
					var mglist = val.Resource.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.Resource.ResourceId).html($htmlmg);
				}			

				jQuery(".container"+val.Resource.ResourceId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( ".nameAnchor"+val.Resource.ResourceId ).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
				position : { my: 'center bottom', at: 'center top-10' },
				tooltipClass: 'bfi-tooltip bfi-tooltip-top '
			}); 
		},'json');
	}
	


}

function getDiscountsAjaxInformations(discountIds,obj, fn){
	var query = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
			$html = '';
			jQuery.each(data || [], function(key, val) {
				var name = val.Name;
				var descr = val.Description;
				name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
				$html += '<p class="title">' + name + '</p>';
				descr = bookingfor.nl2br(jQuery("<p>" + descr + "</p>").text());
				$html += '<p class="description ">' + bookingfor.stripbbcode(descr) + '</p>';
			});
			offersLoaded[discountIds] = $html;
			fn(obj,$html);
	},'json');

}

var offersLoaded = []

jQuery(document).ready(function() {
	getAjaxInformations();

	jQuery('.bfi-maps-static,.bfi-search-view-maps').click(function() {
		jQuery( "#bfi-maps-popup" ).dialog({
			open: function( event, ui ) {
				openGoogleMapSearch();
			},
			height: 500,
			width: 800,
			dialogClass: 'bfi-dialog bfi-dialog-map'
		});
	});
	
	jQuery('.bfi-sort-item').click(function() {
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

	jQuery(".bfi-discounted-price").on("click", function (e) {
		e.preventDefault();
		var bfi_wuiP_width= 400;
		if(jQuery(window).width()<bfi_wuiP_width){
			bfi_wuiP_width = jQuery(window).width()*0.7;
		}
		var showdiscount = function (obj, text) {
							obj.find("i").first().switchClass("fa-spinner fa-spin","fa-question-circle")
							obj.webuiPopover({
								content : text,
								container: document.body,
								closeable:true,
								placement:'auto-bottom',
								dismissible:true,
								trigger:'manual',
								type:'html',
								width : bfi_wuiP_width,
								style:'bfi-webuipopover'
							});
							obj.webuiPopover('show');

		};
		var discountIds = jQuery(this).attr('rel');

		if (!bookingfor.offersLoaded.hasOwnProperty(discountIds)) {
			jQuery(this).find("i").first().switchClass("fa-question-circle","fa-spinner fa-spin")
			bookingfor.GetDiscountsInfo(discountIds,"<?php echo $language ?>", jQuery(this), showdiscount);

		} else {
			showdiscount(jQuery(this), bookingfor.offersLoaded[discountIds]);
		}
	});		
//		jQuery(".bfi-percent-discount").on("blur", function (e) {
//			jQuery(this).webuiPopover('hide');
//		});	
	jQuery(".bfi-discounted-price").focusout(function () {
		jQuery(this).webuiPopover('hide');
	});	

});


	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.Resource.XGooglePos == '' || val.Resource.YGooglePos == '' || val.Resource.XGooglePos == null || val.Resource.YGooglePos == null)
				return true;
			var query = "resourceId=" + val.Resource.CondominiumId + '&language=<?php echo $language ?>&task=getmarketinfocondominium';
			if(val.Resource.CondominiumId == val.Resource.ResourceId ){
				query = "resourceId=" + val.Resource.CondominiumId + '&language=<?php echo $language ?>&task=getmarketinforesource';
			}
			var marker = new MarkerWithLabel({
				position: new google.maps.LatLng(val.Resource.XGooglePos, val.Resource.YGooglePos),
				map: currentMap,
				labelContent: val.Resource.Price>0?val.Resource.Price:val.Resource.ResourceName,
				labelAnchor: new google.maps.Point(22, 0),
				labelClass: ("bfi-labels-marker " + (val.Resource.Price>0?' bfi_<?php echo $currencyclass ?> ':'')) , // the CSS class for the label
				labelStyle: {opacity: 0.75}

			});
			marker.url = bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + query;
			marker.extId = val.Resource.ResourceId;

			oms.addMarker(marker);
					
			bounds.extend(marker.position);
		});
	}



//-->
</script>