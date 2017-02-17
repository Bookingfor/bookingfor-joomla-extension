<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document     = JFactory::getDocument();
$language     = $document->getLanguage();

$config = $this->config;
$isportal = $config->get('isportal', 1);
$usessl = $config->get('usessl', 0);

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = intval($db->loadResult());

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemIdMerchant = intval($db->loadResult());


$resource = $this->item;

//echo "<pre>";
//echo print_r($resource);
//echo "</pre>";

$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$uriResource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
if ($itemId<>0)
	$uriResource .= '&Itemid='.$itemId;

$route = JRoute::_($uriResource);

$merchant = $resource->Merchant;
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant = JRoute::_($uriMerchant);

$merchantLogo = JURI::base() ."/media/com_bookingfor/images/default.png";
if ($merchant!= null && $merchant->LogoUrl != '') {
		$merchantLogo = BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl ,'logobig');
}

$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$completestay = null;
$stay = null;
$stayAvailability = 0;
$DiscountedPrice = 0;
$total = 0;
$calPrices = null;
$calPkgs = null;
$selPriceType = null;
$selPriceTags = '';
$priceTypes = $this->PriceTypes;

$calPricesResources = null; //prezzi calcolari per risorsa e persone
$calPricesServices = null; //prezzi calcolati per servizi extra
$SimpleDiscountIds ="";

if(!empty($this->stay)) {
	$stay = $this->stay->SuggestedStay;
	$completestay = $this->stay;
	if(!empty($stay->DiscountedPrice)){
		$DiscountedPrice = (float)$stay->DiscountedPrice;
	}
	if(!empty($stay->TotalPrice)){
		$total = (float)$stay->TotalPrice;
	}
	if(isset($stay->Availability)){
		$stayAvailability = $stay->Availability;
	}
	if(!empty($completestay->SimpleDiscountIds)){
		$SimpleDiscountIds= implode(',', $completestay->SimpleDiscountIds);	
	}
}


$checkin = new JDate('now'); 
$checkout = new JDate('now'); 
$paxes = 2;
$paxages = array();
$merchantCategoryId = 0;

$pars = $this->params;

if (!empty($pars)){

	if (!empty($pars['checkin'])){
		$checkin = new JDate($pars['checkin']->format('Y-m-d')); 		
	}
	if (!empty($pars['checkout'])){
		$checkout = new JDate($pars['checkout']->format('Y-m-d')); 
	}
	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['merchantCategoryId'])) {
		$merchantCategoryId = $pars['merchantCategoryId'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	if ($pars['checkout'] == null){
		$checkout->modify($checkoutspan); 
	}
}


$allStaysToView = array();
$allstaysuggested= array(); //used in form to get all data 

function pushStay($arr, $resourceid, $resStay, $defaultResource = null,&$staysuggesteds) {
	$selected = array_values(array_filter($arr, function($itm) use ($resourceid) {
		return $itm->ResourceId == $resourceid;
	}));
	$index = 0;
	if(count($selected) == 0) {
		$obj = new stdClass();
		$obj->ResourceId = $resourceid;
		if(isset($defaultResource) && $defaultResource->ResourceId == $resourceid) {
			$obj->MinCapacityPaxes = $defaultResource->MinCapacityPaxes;
			$obj->MaxCapacityPaxes = $defaultResource->MaxCapacityPaxes;
			$obj->Name = $defaultResource->Name;
			$obj->ImageUrl = $defaultResource->ImageUrl;
			$obj->Availability = $defaultResource->Availability;
			$obj->Policy = $resStay->Policy;
		} else {
			$obj->MinCapacityPaxes = $resStay->MinCapacityPaxes;
			$obj->MaxCapacityPaxes = $resStay->MaxCapacityPaxes;
			$obj->Availability = $resStay->Availability;
			$obj->Name = $resStay->ResName;
			$obj->ImageUrl = $resStay->ImageUrl;
			$obj->Policy = $resStay->Policy;
		}
		$obj->RatePlans = array();
		//$obj->Policy = $completestay->Policy;
		//$obj->Description = $singleRateplan->Description;
		$arr[] = $obj;
		$index = count($arr) - 1;
	} else {
		$index = array_search($selected[0], $arr);
		//$obj = $selected[0];
	}

	$rt = new stdClass();
	$rt->RatePlanId = $resStay->RatePlanId;
	$rt->Name = $resStay->Name;	
	$rt->PercentVariation = $resStay->PercentVariation;	
	
	$rt->TotalPrice=0;
	$rt->TotalPriceString ="";
	$rt->Days=0;
	$rt->BookingType=$resStay->BookingType;
	$rt->IsBase=$resStay->IsBase;

	$rt->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
	$rt->SelectablePrices = $resStay->SelectablePrices;
	$rt->Variations = $resStay->Variations;
	$rt->SimpleDiscountIds = implode(',', $resStay->SimpleDiscountIds);	
	if(!empty($resStay->SuggestedStay->DiscountedPrice)){
		$rt->TotalPrice = (float)$resStay->SuggestedStay->TotalPrice;
		$rt->TotalPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->TotalPrice);
		$rt->Days = $resStay->SuggestedStay->Days;
		$rt->DiscountedPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->DiscountedPrice);
		$rt->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
	}
//	$rt->SuggestedStay = $resStay->SuggestedStay;
	$rt->originalStay = $resStay;
	$rt->DiscountVariation = $resStay->DiscountVariation;
	$rt->SupplementVariation = $resStay->SupplementVariation;

	$arr[$index]->RatePlans[] = $rt;

	$currstaysuggested = "";
	if(!empty($resStay->SuggestedStay)){

		$tmpcurrstay = $resStay->SuggestedStay;
		$tmpcurrstay->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
		$tmpcurrstay->RatePlanStay = $resStay;
		unset($tmpcurrstay->RatePlanStay->SuggestedStay);
		$tmpcurrstay->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
	//	$tmpcurrstay->Variations = $selVariation;
		$tmpcurrstay->DiscountVariation = $resStay->DiscountVariation;
		$tmpcurrstay->SupplementVariation = $resStay->SupplementVariation;
		$currstaysuggested = htmlspecialchars(json_encode($tmpcurrstay), ENT_COMPAT, 'UTF-8');
	}
	//echo "<pre>currstaysuggested:<br />";
	//echo print_r($currstaysuggested);
	//echo "</pre>";
	$staysuggesteds[$resStay->BookingType] = $currstaysuggested;

	return $arr;
}

$hasResourceStay = false;

if(isset($this->resstays)) {
	$hasResourceStay = true;
	foreach($this->resstays as $rst) {

		$allStaysToView = pushStay($allStaysToView, $resource->ResourceId, $rst, $resource,$allstaysuggested);
	}
}

$this->allstaysuggested = $allstaysuggested;

//foreach($this->allstays as $rst) {
//	$allStaysToView = pushStay($allStaysToView, $rst->ResourceId, $rst);
//}

$duration = $checkin->diff($checkout);

$showQuote = ($DiscountedPrice > 0 && $stayAvailability > 0);

$selPriceTypeObj = null;
if(isset($completestay->CalculatedPricesDetails)){
	$calPrices = $completestay->CalculatedPricesDetails;
}

if(!empty($completestay)){
	$selPriceType = $completestay->RatePlanId;
	$selPriceTags = $completestay->Tags;
}

if(empty($priceTypes)){
$priceTypes = [];
}

$priceTypes = array_filter($priceTypes, function($priceType) use ($selPriceTags) {
	return $priceType->Tags == $selPriceTags;
});

if (count($priceTypes) > 0){
	foreach ($priceTypes as $pt) {
		if ($pt->Type != $selPriceType)  { 
			continue;
		}
		$selPriceTypeObj = $pt;
		break;
	}
}


$nad = 0;
$nch = 0;
$nse = 0;
$countPaxes = 0;
$nchs = array(null,null,null,null,null,null);

if (empty($paxages)){
	$nad = 2;
}else{
	if(is_array($paxages)){
		$countPaxes = array_count_values($paxages);
		$nchs = array_values(array_filter($paxages, function($age) {
			if ($age < (int)BFCHelper::$defaultAdultsAge)
				return true;
			return false;
		}));
	}

}
//array_push($nchs, null,null,null,null,null,null);

if($countPaxes>0){
	foreach ($countPaxes as $key => $count) {
		if ($key >= BFCHelper::$defaultAdultsAge) {
			if ($key >= BFCHelper::$defaultSenioresAge) {
				$nse += $count;
			} else {
				$nad += $count;
			}
		} else {
			$nch += $count;
		}
	}
}

$bookingTypeFrpmForm = BFCHelper::getVar('bookingType');

$bookingTypedefault=0;
$bookingTypesValues = array();
$bookingTypes = $this->MerchantBookingTypes;

if(!empty($bookingTypes)){
	foreach($bookingTypes as $bt)
	{

		$calculatedBookingType = $bt;
		$bookingTypesValues[$bt->BookingTypeId] = $calculatedBookingType;

		if($bt->IsDefault == true ){
			$bookingTypedefault = $bt->BookingTypeId;
		}

	}

	if(empty($bookingTypedefault)){
		$bt = array_values($bookingTypesValues)[0]; 
		$bookingTypedefault = $bt->BookingTypeId;
	}
}

if(!empty($bookingTypeFrpmForm)){
		if (array_key_exists($bookingTypeFrpmForm, $bookingTypesValues)) {
			$bookingTypedefault = $bookingTypeFrpmForm;
		}
}



?>
<style type="text/css">
@media (min-width: 768px) {
  .uk-width-medium-7-10 {
    width: 100% !important;
  }
}
</style>

<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9 com_bookingforconnector_resource-payment-form">
	<?php if ($showQuote):?>
		  <div class="com_bookingforconnector_resource-calculator-requestForm">
			<?php echo  $this->loadTemplate('inforequest');?>
			<br /><br />
		  </div>
	<?php else:?>		
		<div class="errorbooking" id="errorbooking">
			<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong><br />
			<a href="<?php echo $route ?>">back</a>
		</div>
	<?php endif;?>		
	</div>
<!-- Summary dx -->
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 bf-summary">
		<div class="bf-summary-logo"><a href="<?php echo $routeMerchant?>"><img  src="<?php echo $merchantLogo; ?>"></a></div>
		<!-- Summary header -->
		<div class="bf-summary-header">
			<div class="bf-summary-title"><i class="fa fa-suitcase"></i>&nbsp;<?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SUMMARY_TITLE') ?></div>
			<div class="bf-summary-header-name"><a href="<?php echo $routeMerchant?>"><?php echo  $merchant->Name?></a></div>
			<div class="bf-summary-header-rating">
			<?php for($i = 0; $i < $merchant->Rating ; $i++) { ?>
			  <i class="fa fa-star"></i>
			<?php } ?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-calendar" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>: <?php echo $checkin->format('d/m/Y') ?></div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-calendar" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>: <?php echo $checkout->format('d/m/Y') ?></div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-user" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11">	
					<?php echo $nad ?> <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ADULTS') ?>
					<?php if ($nse > 0): ?><?php if ($nad > 0): ?>, <?php endif; ?>
						<?php echo $nse ?> <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SENIORES') ?>
					<?php endif; ?>
					<?php if ($nch > 0): ?>
						, <?php echo $nch ?> <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDREN') ?> (<?php echo implode(" ".JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_YEAR') .', ',$nchs) ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_YEAR') ?> )
					<?php endif; ?>
				</div>
			</div>
		</div>
		<!-- Summary header -->
		<?php if ($showQuote):?>
			
<?php 			
$firstresource = array_values($allStaysToView)[0]; 
$allRateplans = $firstresource->RatePlans;
		foreach($allRateplans as $currentStay){
//echo "<pre>BookingType:";
//echo $currentStay->BookingType;
//echo "</pre>";



		$calPricesResources = null; //prezzi calcolari per risorsa e persone
		$calPricesServices = null; //prezzi calcolati per servizi extra
		
		$currentCalPrices = $currentStay->CalculatedPricesDetails;
		$isBase =  $currentStay->IsBase;
		
		$currentDiscountedPrice = $currentStay->DiscountedPrice;
		$currentTotal = $currentStay->TotalPrice;
		$currentPercentVariation = $currentStay->PercentVariation; 
		$currentSimpleDiscountIds = $currentStay->SimpleDiscountIds; 
		$currentDeposit = 0;

		$calPricesResources = array_filter($currentCalPrices, function($calPrice)  {
			return $calPrice->Tag=="person" || $calPrice->Tag=="default" || $calPrice->Tag=="";
		});
		$calPricesServices = array_filter($currentCalPrices, function($calPrice)  {
			return $calPrice->Tag<>"default" && $calPrice->Tag<>"person" && $calPrice->Tag<>"" ;
		});


//echo "<pre>singlestay:";
//echo print_r($singlestay);
//echo "</pre>";
//echo "<pre>bookingTypedefault:";
//echo $bookingTypedefault;
//echo "</pre>";
		$currentBookingType = $bookingTypesValues[$currentStay->BookingType];
		if(isset($currentBookingType->Value) && !empty($currentBookingType->Value)) {
			if($currentBookingType->Value!='0' && $currentBookingType->Value!='0%' && $currentBookingType->Value!='100%')
			{
				if (strpos($currentBookingType->Value,'%') !== false) {
					$currentDeposit= (float)str_replace("%","",$currentBookingType->Value) *(float) $currentDiscountedPrice/100;
				}else{
					$currentDeposit= (float) $calculatedBookingType->Value;
				}
			}
			if($currentBookingType->Value==='100%'){
				$currentDeposit = (float)$currentDiscountedPrice;
			}
		}
//echo "<pre>Value:";
//echo $currentBookingType->Value;
//echo "</pre>";
//echo "<pre>currentDeposit:";
//echo $currentDeposit;
//echo "</pre>";
?>
		<div class="bf-bBookingType" id="bf-bBookingType<?php echo $currentStay->BookingType ?>" >
			<div class="bf-summary-body" >
				<div class="bf-summary-body-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_RESOURCES') ?>:</div>
				<div class="bf-summary-body-resourcename">
					<?php echo $resourceName ?>
					<?php if ($selPriceTypeObj != null && $isBase!="True"): ?>
						<br/>
						<?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TREATMENT') ?>: <?php echo BFCHelper::getLanguage($selPriceTypeObj->Name, $this->language) ?>
					<?php endif; ?>
				</div>
			<?php if(!empty($calPricesResources)){ ?>
				<div class="bf-summary-body-resourceprice">
					<?php 
					$totalPriceResource = 0; //prezzo della risorsa + persone
					$totalPriceDiscountedResource = 0; //prezzo della risorsa + persone
					foreach($calPricesResources as $calPrice){
								$totalPriceResource += $calPrice->TotalAmount;
								$totalPriceDiscountedResource += $calPrice->TotalDiscounted;
						}
					if($totalPriceResource > $totalPriceDiscountedResource){ ?>
						<span class="bf-summary-body-resourceprice-total-strike">&euro; <?php echo BFCHelper::priceFormat($totalPriceResource); ?></span>
					<?php 
					}
					if($totalPriceDiscountedResource > 0){ ?>
						<span class="bf-summary-body-resourceprice-total">&euro; <?php echo BFCHelper::priceFormat($totalPriceDiscountedResource); ?></span>
					<?php 
					}
					?>

				</div>
			<?php } ?>
			<?php if(!empty($calPricesServices)){ ?>
				<hr />
				<div class="bf-summary-body-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SERVICES') ?>:</div>
					<?php foreach($calPricesServices as $calPrice):?>
							<div class="bf-summary-body-resourcename">
								<?php echo $calPrice->Name ?>
							</div>
							<div class="bf-summary-body-resourceprice">
								<?php if($calPrice->TotalAmount  <> $calPrice->TotalDiscounted ){ ?>
									<span class="bf-summary-body-resourceprice-total-strike">&euro; <?php echo BFCHelper::priceFormat($calPrice->TotalAmount );?></span>
								<?php } ?>
								<span class="bf-summary-body-resourceprice-total">&euro; <?php echo BFCHelper::priceFormat($calPrice->TotalDiscounted );?></span>
							</div>
					<?php endforeach;?>
			<?php } ?>

			</div>
			<div class="bf-summary-footer">
				<div class="bf-summary-footer-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_TOTALPRICE') ?>:</div>
							<div class="com_bookingforconnector_merchantdetails-resource-stay-price originalquote" style=" <?php echo $currentDiscountedPrice >= $currentTotal ? "display:none;" : ""; ?>">
								<span class="com_bookingforconnector_resourcelist_strikethrough <?php echo $DiscountedPrice >= $total ? "notvars" : "" ?>"><span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight">&euro; <?php echo  BFCHelper::priceFormat($currentTotal) ?></span></span>
							</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div style="<?php echo $DiscountedPrice >= $total ? "display:none;" : ""; ?>">
								<div class="specialoffer variationlabel " style="<?php echo $currentDiscountedPrice >= $currentTotal ? "display:none;" : ""; ?>" rel="<?php echo $currentSimpleDiscountIds ?>" rel1="<?php echo  $resource->ResourceId ?>_<?php echo $currentStay->BookingType ?>" >
									<span class="variationlabel_percent"><?php echo $currentPercentVariation ?></span>% <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
								</div>
							</div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 bf-summary-footer-totalprice">
						&euro; <?php echo BFCHelper::priceFormat($currentDiscountedPrice); ?>
					</div>
				</div>
				<div class="divoffers" id="divoffers<?php echo  $resource->ResourceId ?>_<?php echo $currentStay->BookingType ?>" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
						<span class="sr-only">Loading...</span>
				</div>
				<div class="bf-summary-footer-deposit" style="margin-top:5px;<?php echo $currentDeposit >0 ? "" : "display:none;"; ?>">
					<div class="bf-summary-footer-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT') ?>:</div>
					<div class="bf-summary-footer-totalprice" id="footer-deposit">&euro; <?php echo  BFCHelper::priceFormat($currentDeposit) ?></div>
				</div>
			</div>
		</div>

<?php 			
		}
?>

	<?php endif;?>		
</div>
</div>

<script type="text/javascript">
<!--
	var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
	var cultureCode = '<?php echo $language ?>';
	var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
	var offersLoaded = [];

	jQuery(function($)
		{
			$("#aside").hide();
			$("#content").removeClass().addClass("<?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>12");

			$(".tm-main").removeClass().addClass("tm-main uk-width-medium-1-1");
			$(".tm-sidebar-b").hide();
			$(".t3-sidebar").hide();
			$(".ja-content").removeClass().addClass("ja-content <?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>12");
			$(".ja-sidebar").hide();
			$("#content_right").hide();
			$("#content_left").hide();
			$("#content_main").removeClass().addClass("<?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>12");
			$("#t3-content").removeClass().addClass("<?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>12");
			// bind resize 
			if (jQuery.prototype.smartresize){
				$(window).smartresize(function(){
					$(".ja-content").removeClass().addClass("ja-content <?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>12");
				});
			}

			jQuery(".variationlabel").on("click", function(){
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
					jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
				  } else {
					jQuery("#divoffers"+resourceId).slideDown("slow");
					jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
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
		});

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
//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
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

//-->
</script>
