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
$currCart = null;
$totalOrder = 0;
$currencyclass = bfi_get_currentCurrency();
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;

$CartMultimerchantEnabled = BFCHelper::getCartMultimerchantEnabled(); 
$listStayConfigurations = array();
$dateTimeNow =  new DateTime();

if (isset($_POST['hdnOrderData']) && isset($_POST['hdnPolicyIds'])  ) {
	$policyByIds = $_POST['hdnPolicyIds'];
	$hdnOrderData = $_POST['hdnOrderData'];
// WP =>	$listResources = json_decode(stripslashes($hdnOrderData));
	$listResources = json_decode($hdnOrderData);
	
	foreach ($listResources as $keyRes=>$res) {
		
		$currCheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate);
		$currCheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate);
		if ($res->AvailabilityType == 2)
		{
			
			$currCheckIn = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
			$currCheckOut = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
			$currCheckOut->add(new DateInterval('PT' . $res->TimeDuration . 'M'));
		}		
		if ($res->AvailabilityType == 3)
		{
			
			$currCheckIn = DateTime::createFromFormat("Ymd", $res->FromDate);
			$currCheckOut = clone $currCheckIn;
			$currCheckIn->setTime(0,0,1);
			$currCheckOut->setTime(0,0,1);
			$currCheckIn->add(new DateInterval('PT' . $res->TimeSlotStart . 'M'));
			$currCheckOut->add(new DateInterval('PT' . $res->TimeSlotEnd . 'M'));
		}		
		$currStayConfiguration = array("productid"=>$res->ResourceId,"price"=>$res->TotalAmount,"start"=>$currCheckIn->format("Y-m-d\TH:i:s"),"end"=> $currCheckOut->format("Y-m-d\TH:i:s"));
		$listStayConfigurations[] = $currStayConfiguration;
	}
		
	$currPolicy = BFCHelper::GetMostRestrictivePolicyByIds($policyByIds, $language,json_encode($listStayConfigurations));
	
	BFCHelper::setSession('hdnPolicyId', $currPolicy->PolicyId, 'bfi-cart');
	BFCHelper::setSession('hdnOrderData', $hdnOrderData, 'bfi-cart');

}
$sessionBookingType = BFCHelper::getSession('hdnPolicyId', '', 'bfi-cart');
$sessionOrderData = BFCHelper::getSession('hdnOrderData', '', 'bfi-cart');


if (!empty($sessionBookingType) && !empty($sessionOrderData)  ) {
	$bookingType = $sessionBookingType;
	$hdnOrderData = $sessionOrderData;
	$currCart= new stdClass;
// WP =>	$currCart->CartConfiguration = stripslashes('{"Resources":' .$hdnOrderData . ',"SearchModel":null,"PolicyId":'.$bookingType.',"TotalAmount":0.0,"TotalDiscountedAmount":0.0,"CartOrderId":null}');
	$currCart->CartConfiguration = ('{"Resources":' .$hdnOrderData . ',"SearchModel":null,"PolicyId":'.$bookingType.',"TotalAmount":0.0,"TotalDiscountedAmount":0.0,"CartOrderId":null}');
}


if($CartMultimerchantEnabled && empty($currCart)){
	$tmpUserId = BFCHelper::bfi_get_userId();
	$currCart = BFCHelper::GetCartByExternalUser($tmpUserId, $language, true);
}


if($CartMultimerchantEnabled){

	$cartEmpty=empty($currCart);
	$cartConfig = null;
	if(!$cartEmpty){
		$cartConfig = json_decode(($currCart->CartConfiguration));
		if(isset($cartConfig->Resources) && count($cartConfig->Resources)>0){
			$cartEmpty = false;
		}else{
			$cartEmpty = true;
		}

	}

	if($cartEmpty){
		echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_EMPTY');
	}else{

$allResourceId = array();
$allServiceIds = array();
$allPolicyHelp = array();

$cNationList = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_NATIONSLIST'));
$cultureCode = strtolower(substr($this->language, 0, 2));
$nationCode = strlen($this->language) == 5 ? strtolower(substr($this->language, 3, 2)) : $cultureCode;
$keys = array_keys($cNationList);
$nations = array_values(array_filter($keys, function($item) use($nationCode) {
	return strtolower($item) == $nationCode; 
	}
));
$nation = !empty(count($nations)) ? $nations[0] : $cultureCode;




?>
<div class="bf-summary-cart">
<?php 



//		$modelMerchant = new BookingForConnectorModelMerchantDetails;
//		$modelResource = new BookingForConnectorModelResource;
//		$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
//		$url_merchant_page = get_permalink( $merchantdetails_page->ID );
		$db   = JFactory::getDBO();
		$uri  = 'index.php?option=com_bookingforconnector&view=resource';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemId = intval($db->loadResult());
		if ($itemId<>0){
			$uri.='&Itemid='.$itemId;
		}

		$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
		if ($itemIdMerchant<>0)
			$uriMerchant.='&Itemid='.$itemIdMerchant;
		
		$uriCart  = 'index.php?option=com_bookingforconnector&view=cart';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriCart .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemIdCart= ($db->getErrorNum())? 0 : intval($db->loadResult());
		if ($itemIdCart<>0)
			$uriCart.='&Itemid='.$itemIdCart;
		$url_cart_page = JRoute::_($uriCart);
		if($usessl){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}


		$cartId= isset($currCart->CartId)?$currCart->CartId:0;
//		$config = json_decode($currCart->CartConfiguration);

		$cCCTypeList = array();
		$minyear = date("y");
		$maxyear = $minyear+5;
//		$formRoute = $base_url .'/bfi-api/v1/task?task=sendOrders'; 
		$formRoute = "index.php?option=com_bookingforconnector&task=sendOrders"; 
		$formRouteDelete = "index.php?option=com_bookingforconnector&task=DeleteFromCart"; 

		$privacy = BFCHelper::GetPrivacy($language);
		$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);

		$policyId = $cartConfig->PolicyId;
		$currPolicy =  BFCHelper::GetPolicyById($policyId, $language);
		
		$deposit = 0;
		$totalWithVariation	 = 0;
		$totalAmount = 0;	
		$totalQt = 0;	
		$listMerchantsCart = array();
		$listResourcesCart = array();
		$listResourceIdsToDelete = array();
		$now = new DateTime();
		$now->setTime(0,0,0);

		// cerco risorse scadute come checkin
		foreach ($cartConfig->Resources as $keyRes=>$resource) {
			$id = $resource->ResourceId;
			$merchantId = $resource->MerchantId;
			$listResourcesCart[] = $id;
			$tmpCheckinDate = new DateTime();
			if($cartId==0){
				$tmpCheckinDate = DateTime::createFromFormat('d/m/Y\TH:i:s', $resource->FromDate);
//				$tmpCheckinDate->setTime(0,0,1);
			}else{
				$tmpCheckinDate = new DateTime($resource->FromDate);
			}
			if($tmpCheckinDate < $now){
				if($cartId==0){
					unset($cartConfig->Resources[$keyRes]);  
				}else{
					$listResourceIdsToDelete[] = $resource->CartOrderId;
				}
			}
						
			if (isset($listMerchantsCart[$merchantId])) {
				$listMerchantsCart[$merchantId][] = $resource;
			} else {
				$listMerchantsCart[$merchantId] = array($resource);
			}
			
			if(!empty($resource->ExtraServices)) { 
				foreach($resource->ExtraServices as $sdetail) {					
					$listResourcesCart[] = $sdetail->PriceId;
				}
			}

		}
		if(count($listResourceIdsToDelete)>0){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$currCart = BFCHelper::DeleteFromCartByExternalUser($tmpUserId, $language, implode(",", $listResourceIdsToDelete));
			$app = JFactory::getApplication();
			$app->redirect($url_cart_page, false);
			$app->close();
		}
		if($cartId==0){
			BFCHelper::setSession('hdnOrderData', json_encode($cartConfig->Resources), 'bfi-cart');
		}

		$tmpResources =  json_decode(BFCHelper::GetResourcesByIds(implode(",", $listResourcesCart),$language));

				
		$resourceDetail = array();
		$merchantDetail = array();

		foreach ($tmpResources as $resource) {
			$resourceId = $resource->Resource->ResourceId;
			if (!isset($resourceDetail[$resourceId])) {
				$resourceDetail[$resourceId] = $resource->Resource;
			}
			$merchantId = $resource->Merchant->MerchantId;
			if (!isset($merchantDetail[$merchantId])) {
				$merchantDetail[$merchantId] = $resource->Merchant;
			}
		}

?>	 
<div class="bfi-content">
<div class="bfi-cart-title"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TITLE') ?></div>

	<?php  include(JPATH_COMPONENT.'/views/shared/menu_small_booking.php');  ?>
<script type="text/javascript">
<!--
	jQuery(function()
	{
		jQuery(".bfi-menu-booking a:eq(2)").removeClass(" bfi-alternative3");
	});
//-->
</script>
	<div class="bfi-border bfi-cart-title2"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TITLE2') ?></div>
<div class="bfi-table-responsive">
		<table class="bfi-table bfi-table-bordered bfi-table-cart" style="margin-top: 20px;">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_INFO') ?></th>
					<th><div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FOR') ?></div></th>
					<th ><div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_PRICE') ?></div></th>
					<th><div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_OPTIONS') ?></div></th>
					<th><div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_QT') ?></div></th>
					<th><div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_TOTALPRICE') ?></div></th>
				</tr>
			</thead>
<?php
	foreach ($listMerchantsCart as $merchant_id=>$merchantResources) // foreach $listMerchantsCart
	{
		$MerchantDetail = $merchantDetail[$merchant_id];  //$modelMerchant->getItem($merchant_id);	 
		
		$currUriMerchant = $uriMerchant. '&merchantId=' . $MerchantDetail->MerchantId. ':' . BFCHelper::getSlug($MerchantDetail->Name);
		$routeMerchant = JRoute::_($currUriMerchant);

		$nRowSpan = 1;
		
		$rating = $MerchantDetail->Rating;
		if ($rating>9 )
		{
			$rating = $rating/10;
		} 
		$mrcindirizzo = "";
		$mrccap = "";
		$mrccomune = "";
		$mrcstate = "";

		if (empty($MerchantDetail->AddressData)){
			$mrcindirizzo = isset($MerchantDetail->Address)?$MerchantDetail->Address:""; 
			$mrccap = isset($MerchantDetail->ZipCode)?$MerchantDetail->ZipCode:""; 
			$mrccomune = isset($MerchantDetail->CityName)?$MerchantDetail->CityName:""; 
			$mrcstate = isset($MerchantDetail->StateName)?$MerchantDetail->StateName:""; 
		}else{
			$addressData = isset($MerchantDetail->AddressData)?$MerchantDetail->AddressData:"";
			$mrcindirizzo = isset($addressData->Address)?$addressData->Address:""; 
			$mrccap = isset($addressData->ZipCode)?$addressData->ZipCode:""; 
			$mrccomune = isset($addressData->CityName)?$addressData->CityName:""; 
			$mrcstate = isset($addressData->StateName)?$addressData->StateName:"";
		}
		
		foreach ($merchantResources as $res )
		{
			$nRowSpan += 1;
			if(!empty($res->ExtraServices)) { 
				foreach($res->ExtraServices as $sdetail) {					
					$nRowSpan += 1;
				}
			}
		}
$mrcAcceptanceCheckInHours=0;
$mrcAcceptanceCheckInMins=0;
$mrcAcceptanceCheckInSecs=1;
$mrcAcceptanceCheckOutHours=0;
$mrcAcceptanceCheckOutMins=0;
$mrcAcceptanceCheckOutSecs=1;
if(!empty($MerchantDetail->AcceptanceCheckIn) && !empty($MerchantDetail->AcceptanceCheckOut) && $MerchantDetail->AcceptanceCheckIn != "-" && $MerchantDetail->AcceptanceCheckOut != "-"){
	$tmpAcceptanceCheckIn=$MerchantDetail->AcceptanceCheckIn;
	$tmpAcceptanceCheckOut=$MerchantDetail->AcceptanceCheckOut;
	$tmpAcceptanceCheckIns = explode('-', $tmpAcceptanceCheckIn);
	$tmpAcceptanceCheckOuts = explode('-', $tmpAcceptanceCheckOut);	
	list($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs) = explode(':',$tmpAcceptanceCheckIns[0].":1");
	list($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs) = explode(':',$tmpAcceptanceCheckOuts[1].":1");
}

?>
			<tr >
				<td colspan="6" class="bfi-merchant-cart">
					<div class="bfi-item-title">
						<a href="<?php echo $isportal?$routeMerchant:"#";?>" ><?php echo $MerchantDetail->Name?></a>
						<span class="bfi-item-rating">
							<?php for($i = 0; $i < $rating; $i++) { ?>
								<i class="fa fa-star"></i>
							<?php } ?>	             
						</span>
					</div>
					<br />
					<span class="street-address"><?php echo $mrcindirizzo ?></span>, <span class="postal-code "><?php echo $mrccap ?></span> <span class="locality"><?php echo $mrccomune ?></span> <span class="state">, <?php echo $mrcstate ?></span><br />

				</td>
			</tr>
			<?php 
			foreach ($merchantResources as $keyRes=>$res )
			{
				$nad = 0;
				$nch = 0;
				$nse = 0;
				$countPaxes = 0;

				if($cartId==0){
					$res->CartOrderId = $keyRes;  
				}
				$nchs = array(null,null,null,null,null,null);
					$paxages = $res->PaxAges;
					if(is_array($paxages)){
						$countPaxes = array_count_values($paxages);
						$nchs = array_values(array_filter($paxages, function($age) {
							if ($age < (int)BFCHelper::$defaultAdultsAge)
								return true;
							return false;
						}));
					}
				array_push($nchs, null,null,null,null,null,null);
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

				
				$countPaxes = $res->PaxNumber;

				$nchs = array_slice($nchs,0,$nch);
				$resource = $resourceDetail[$res->ResourceId];  //$modelMerchant->getItem($merchant_id);	 
											
				$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resource->Name);
				$routeResource = JRoute::_($currUriresource);
				
				$totalPricesExtraIncluded = 0;
				$totalAmountPricesExtraIncluded = 0;
				$pricesExtraIncluded = null;

				if(!empty( $res->PricesExtraIncluded )){
					$pricesExtraIncluded = json_decode($res->PricesExtraIncluded);
					foreach($pricesExtraIncluded as $sdetail) {					
						$totalPricesExtraIncluded +=$sdetail->TotalDiscounted;
						$totalAmountPricesExtraIncluded +=$sdetail->TotalAmount;
					}
				}

												
			?>
                                <tr>
                                    <td>
										<div class="bfi-resname">
											<a href="<?php echo $routeResource?>" target="_blank"><?php echo $resource->Name ?></a>
										</div>
										<div class="bfi-cart-person">
											<?php if ($nad > 0): ?><?php echo $nad ?> <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ADULTS') ?> <?php endif; ?>
											<?php if ($nse > 0): ?><?php if ($nad > 0): ?>, <?php endif; ?>
												<?php echo $nse ?> <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SENIORES') ?>
											<?php endif; ?>
											<?php if ($nch > 0): ?>
												, <?php echo $nch ?> <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDREN') ?> (<?php echo implode(" ". JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_YEAR') .', ',$nchs) ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_YEAR') ?> )
											<?php endif; ?>
                                       </div>
								<?php																
								/*-----------checkin/checkout--------------------*/	
									if ($res->AvailabilityType == 0 )
									{
										$currCheckIn = new DateTime();
										$currCheckOut = new DateTime();
										if($cartId==0){
											$currCheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate);
											$currCheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->ToDate);

										}else{
											$currCheckIn = new DateTime($res->FromDate);
											$currCheckOut = new DateTime($res->ToDate);
											$currCheckIn->setTime($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs);
											$currCheckOut->setTime($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs);
										}										
										$currCheckInFull = clone $currCheckIn;
										$currCheckOutFull =clone $currCheckOut;
										$currCheckInFull->setTime(0,0,1);
										$currCheckOutFull->setTime(0,0,1);

										$currDiff = $currCheckOutFull->diff($currCheckInFull);

										$currCheckIn = new JDate($currCheckIn->format('Y-m-d')); 
										$currCheckOut = new JDate($currCheckOut->format('Y-m-d')); 

									?>
										<div class="bfi-timeperiod " >
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_FROM') ?> <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?> <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_UNTIL') ?> <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->d + 1; ?></span> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
									if ($res->AvailabilityType == 1 )
									{
										$currCheckIn = new DateTime();
										$currCheckOut = new DateTime();
										if($cartId==0){
											$currCheckIn = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate);
											$currCheckOut = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->ToDate);
										}else{
											$currCheckIn = new DateTime($res->FromDate);
											$currCheckOut = new DateTime($res->ToDate);
											$currCheckIn->setTime($mrcAcceptanceCheckInHours,$mrcAcceptanceCheckInMins,$mrcAcceptanceCheckInSecs);
											$currCheckOut->setTime($mrcAcceptanceCheckOutHours,$mrcAcceptanceCheckOutMins,$mrcAcceptanceCheckOutSecs);
										}										

										$currCheckInFull = clone $currCheckIn;
										$currCheckOutFull =clone $currCheckOut;
										$currCheckInFull->setTime(0,0,1);
										$currCheckOutFull->setTime(0,0,1);

										$currDiff = $currCheckOutFull->diff($currCheckInFull);

										$currCheckIn = new JDate($currCheckIn->format('Y-m-d')); 
										$currCheckOut = new JDate($currCheckOut->format('Y-m-d')); 
									?>
										<div class="bfi-timeperiod " >
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_FROM') ?> <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?> <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_UNTIL') ?> <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->d; ?></span> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
									if ($res->AvailabilityType == 2)
									{
										
										$currCheckIn = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
										$currCheckOut = DateTime::createFromFormat("YmdHis", $res->CheckInTime);
										$currCheckOut->add(new DateInterval('PT' . $res->TimeDuration . 'M'));
										
										$currDiff = $currCheckOut->diff($currCheckIn);
										$timeDuration = $currDiff->h + ($currDiff->i/60);
										
										$currCheckIn = new JDate($currCheckIn->format('Y-m-d\TH:i:s')); 
										$currCheckOut = new JDate($currCheckOut->format('Y-m-d\TH:i:s')); 
									?>
										<div class="bfi-timeperiod " >
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?> <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDuration ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_HOURS') ?>
												</div>	
											</div>	
										</div>
								<?php
									}
/*-------------------------------*/	
									if ($res->AvailabilityType == 3)
									{

										$currCheckIn = new DateTime($res->FromDate);										
										$currCheckOut = clone $currCheckIn;
										$currCheckIn->setTime(0,0,1);
										$currCheckOut->setTime(0,0,1);
										$currCheckIn->add(new DateInterval('PT' . $res->TimeSlotStart . 'M'));
										$currCheckOut->add(new DateInterval('PT' . $res->TimeSlotEnd . 'M'));

										$currDiff = $currCheckOut->diff($currCheckIn);

										$currCheckIn = new JDate($currCheckIn->format('Y-m-d\TH:i:s')); 
										$currCheckOut = new JDate($currCheckOut->format('Y-m-d\TH:i:s')); 
									?>
										<div class="bfi-timeslot ">
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row ">
												<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
												</div>	
												<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?> <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
												</div>	
											</div>	
											<div class="bfi-row">
												<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
												</div>	
												<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_HOURS') ?>
												</div>	
											</div>	
										</div>
								<?php
									}								

/*-------------------------------*/									
							?>
												<?php 
													$listServices = array();
													if(!empty($resource->ServiceIdList)){
														$listServices = explode(",", $resource->ServiceIdList);
														$allServiceIds = array_merge($allServiceIds, $listServices);
														?>
														<div class="bfisimpleservices" rel="<?php echo $resource->ServiceIdList ?>"></div>
														
														<?php
													}
													if(!empty($resource->TagsIdList)){
														?>
														<div class="bfiresourcegroups" rel="<?php echo $resource->TagsIdList?>"></div>
														<?php
													}					

												$currVat = isset($res->VATValue)?$res->VATValue:"";					
												$currTouristTaxValue = isset($res->TouristTaxValue)?$res->TouristTaxValue:0;				
												?>
												<?php if(!empty($currVat)) { ?>
													<div class="bfi-incuded"><strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_INCLUDED') ?></strong> : <?php echo $currVat?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_VAT') ?> </div>
												<?php } ?>
												<?php if(!empty($currTouristTaxValue)) { ?>
													<div class="bfi-notincuded"><strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_NOTINCLUDED') ?></strong> : <span class="bfi_<?php echo $currencyclass ?>" ><?php echo BFCHelper::priceFormat($currTouristTaxValue) ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CITYTAX') ?> </div>
												<?php } ?>
										
										
                                    </td>
									<td><!-- Min/Max -->
									<?php 
									if(!empty( $res->ComputedPaxes )){
										$computedPaxes = explode("|", $res->ComputedPaxes);
										$nadult =0;
										$nsenior =0;
										$nchild =0;
										
										foreach($computedPaxes as $computedPax) {
											$currComputedPax =  explode(":", $computedPax."::::");
											
											if ($currComputedPax[3] == "0") {
												$nadult += $currComputedPax[1];
											}
											if ($currComputedPax[3] == "1") {
												$nsenior += $currComputedPax[1];
											}
											if ($currComputedPax[3] == "2") {
												$nchild += $currComputedPax[1];
											}
										}

										if ($nadult>0) {
											?>
											<div class="bfi-icon-paxes">
												<i class="fa fa-user"></i> x <b><?php echo $nadult ?></b>
											<?php 
												if (($nsenior+$nchild)>0) {
													?>
													+ <br />
														<span class="bfi-redux"><i class="fa fa-user"></i></span> x <b><?php echo ($nsenior+$nchild) ?></b>
													<?php 
													
												}
											?>
											
											</div>
											
											<?php 
											
										}


									}else{
									?>
										
										<?php if ($res->MaxPaxes>0){?>
											<div class="bfi-icon-paxes">
												<i class="fa fa-user"></i> 
												<?php if ($res->MaxPaxes==2){?>
												<i class="fa fa-user"></i> 
												<?php }?>
												<?php if ($res->MaxPaxes>2){?>
													<?php echo ($res->MinPaxes != $res->MaxPaxes)? $res->MinPaxes . "-" : "" ?><?php echo  $res->MaxPaxes ?>
												<?php }?>
											</div>
										<?php } ?>
									<?php } ?>
									</td>
                                    <td class="text-nowrap"><!-- Unit price -->
                                        <?php if ($res->TotalDiscounted < $res->TotalAmount) { ?>
                                            <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalAmount - $totalAmountPricesExtraIncluded)/$res->SelectedQt); ?></span>
                                        <?php } ?>
                                        <?php if ($res->TotalDiscounted > 0) { ?>
                                            <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalDiscounted - $totalPricesExtraIncluded)/$res->SelectedQt); ?></span>

                                        <?php } ?>
                                    </td>
                                    <td class="text-nowrap">
<!-- options  -->									
					<div style="position:relative;">
					<?php 
$policy = $currPolicy;
$policyId= 0;
$policyHelp = "";
if(!empty( $policy )){
	$currValue = $policy->CancellationBaseValue;
	$policyId= $policy->PolicyId;

	switch (true) {
		case strstr($policy->CancellationBaseValue ,'%'):
			$currValue = $policy->CancellationBaseValue;
			break;
		case strstr($policy->CancellationBaseValue ,'d'):
			$currValue = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS') ,rtrim($policy->CancellationBaseValue,"d"));
			break;
		case strstr($policy->CancellationBaseValue ,'n'):
			$currValue = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS') ,rtrim($policy->CancellationBaseValue,"n"));
			break;
	}
	$currValuebefore = $policy->CancellationValue;
	switch (true) {
		case strstr($policy->CancellationValue ,'%'):
			$currValuebefore = $policy->CancellationValue;
			break;
		case strstr($policy->CancellationValue ,'d'):
			$currValuebefore = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS') ,rtrim($policy->CancellationValue,"d"));
			break;
		case strstr($policy->CancellationValue ,'n'):
			$currValuebefore = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS') ,rtrim($policy->CancellationValue,"n"));
			break;
	}
	if($policy->CanBeCanceled){
		$currTimeBefore = "";
		$currDateBefore = "";
		$currDatePolicy =  new DateTime();
		if($cartId==0){
			$currDatePolicy = DateTime::createFromFormat('d/m/Y\TH:i:s', $res->FromDate);
		}else{
			$currDatePolicy = new DateTime($res->FromDate);
		}										
		if(!empty( $policy->CancellationTime )){		
			switch (true) {
				case strstr($policy->CancellationTime ,'d'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"d") .' days'); 
					break;
				case strstr($policy->CancellationTime ,'h'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"h") .' hours'); 
					break;
				case strstr($policy->CancellationTime ,'w'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"w") .' weeks'); 
					break;
				case strstr($policy->CancellationTime ,'m'):
					$currDatePolicy->modify('-'. rtrim($policy->CancellationTime,"m") .' months'); 
					break;
			}
		}
		if($currDatePolicy > $dateTimeNow){
				$currDatePolicy = new JDate($currDatePolicy->format('Y-m-d')); 
				if(!empty( $policy->CancellationTime )){					
					switch (true) {
						case strstr($policy->CancellationTime ,'d'):
							$currTimeBefore = rtrim($policy->CancellationTime,"d") .' days';	
							break;
						case strstr($policy->CancellationTime ,'h'):
							$currTimeBefore = rtrim($policy->CancellationTime,"h") .' hours';	
							break;
						case strstr($policy->CancellationTime ,'w'):
							$currTimeBefore = rtrim($policy->CancellationTime,"w") .' weeks';	
							break;
						case strstr($policy->CancellationTime ,'m'):
							$currTimeBefore = rtrim($policy->CancellationTime,"m") .' months';	
							break;
					}
				}

				if($policy->CancellationValue=="0" || $policy->CancellationValue=="0%"){
					?>
					<div class="bfi-policy-green"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_FREE') ?>
					<?php 
					if(!empty( $policy->CancellationTime )){
						echo '<br />'.JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_UNTIL');
						echo ' '.$currDatePolicy->format("d").' '.$currDatePolicy->format("M").' '.$currDatePolicy->format("Y");
						$policyHelp = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_FREE_HELP'),$currTimeBefore,$currValue);
					}
					?>
					</div>
					<?php 

					
				}else{
				if($policy->CancellationBaseValue=="0%" || $policy->CancellationBaseValue=="0"){
					?>
					<div class="bfi-policy-green"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_FREE') ?></div>
					<?php 
					$policyHelp = JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_FREE_HELP1');
				}else{
					?>
					<div class="bfi-policy-blue"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_SPECIALCONDITIONS') ?></div>
					<?php 
					$policyHelp = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_SPECIALCONDITIONS_HELP'),$currTimeBefore,$currValue,$currValuebefore);
				}
				}

			
		}else{
				if($policy->CancellationBaseValue=="0%" || $policy->CancellationBaseValue=="0"){
					?>
					<div class="bfi-policy-green"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_FREE') ?></div>
					<?php 
					$policyHelp = JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_FREE_HELP1');
				}else{
					?>
					<div class="bfi-policy-blue"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_SPECIALCONDITIONS') ?></div>
					<?php 
					$policyHelp = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_SPECIALCONDITIONS_HELP1') ,$currValue);
				}
		}
				
	}else{ 
		// no refundable
		?>
			<div class="bfi-policy-none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_NONREFUNDABLE') ?></div>
		<?php 
		$policyHelp = JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_NONREFUNDABLE_HELP');
	
	}
}
if(!empty($policyHelp)){
	$allPolicyHelp[] = $resource->Name . ": " . $policyHelp;
}
//$currMerchantBookingTypes = array();
$prepayment = "";
$prepaymentHelp = "";
//
//if(!empty( $policy->MerchantBookingTypesString )){
//	$currMerchantBookingTypes = json_decode($policy->MerchantBookingTypesString);
//	$currBookingTypeId = $currRateplan->RatePlan->MerchantBookingTypeId;
//	$currMerchantBookingType = array_filter($currMerchantBookingTypes, function($bt) use($currBookingTypeId) {return $bt->BookingTypeId == $currBookingTypeId;});
//	if(count($currMerchantBookingType)>0){
//		if($currMerchantBookingType[0]->PayOnArrival){
//			$prepayment = __("Pay at the property – NO PREPAYMENT NEEDED", 'bfi');
//			$prepaymentHelp = __("No prepayment is needed.", 'bfi');
//		}
//		if($currMerchantBookingType[0]->AcquireCreditCardData){
//			$prepayment = "";
//			if($currMerchantBookingType[0]->DepositRelativeValue=="100%"){
//				$prepaymentHelp = __('You will be charged a prepayment of the total price at any time.', 'bfi');
//			}else if(strpos($currMerchantBookingType[0]->DepositRelativeValue, '%') !== false  ) {
//				$prepaymentHelp = sprintf(__('You will be charged a prepayment of %1$s of the total price at any time.', 'bfi'),$currMerchantBookingType[0]->DepositRelativeValue);
//			}else{
//				$prepaymentHelp = sprintf(__('You will be charged a prepayment of %1$s at any time.', 'bfi'),$currMerchantBookingType[0]->DepositRelativeValue);
//			}
//		}
//	}
//}
$allMeals = array();
$cssclassMeals = "bfi-meals-base";
$mealsHelp = "";
if($res->IncludedMeals >-1){
	$mealsHelp = JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_HELP_NO') ;
	if ($res->IncludedMeals & bfi_Meal::Breakfast){
		$allMeals[]= JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_BREAKFAST') ;
	}
	if ($res->IncludedMeals & bfi_Meal::Lunch){
		$allMeals[]= JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_LUNCH') ;
	}
	if ($res->IncludedMeals & bfi_Meal::Dinner){
		$allMeals[]= JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_DINNER') ;
	}
	if ($res->IncludedMeals & bfi_Meal::AllInclusive){
		$allMeals[]= JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_ALLINCLUSIVE') ;
	}
	if(in_array(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_BREAKFAST'), $allMeals)){
		$cssclassMeals = "bfi-meals-bb";
	}
	if(in_array(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_LUNCH'), $allMeals) || in_array(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_DINNER'), $allMeals) || in_array(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_ALLINCLUSIVE'), $allMeals)  ){
		$cssclassMeals = "bfi-meals-fb";
	}
	if(count($allMeals)>0){
		$mealsHelp = implode(", ",$allMeals). " " . JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_INCLUDED') ;
	}
	if(count($allMeals)==2){
		$mealsHelp = implode(" & ",$allMeals). " " . JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS_INCLUDED') ;
	}
}
?>
<?php if(!empty($prepayment)) { ?>
						<div class="bfi-prepayment"><?php echo $prepayment ?></div>
<?php } ?>

						<div class="bfi-meals <?php echo $cssclassMeals?>"><?php echo $mealsHelp ?></div>
						<div class="bfi-options-help">
							<i class="fa fa-question-circle bfi-cursor-helper" aria-hidden="true"></i>
							<div class="webui-popover-content">
							   <div class="bfi-options-popover">
							   <?php if(!empty($mealsHelp)) { ?>
								   <p><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MEALS') ?>:</b> <?php echo $mealsHelp; ?></p>
							   <?php } ?>
							   <p><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_POLICY_TITLE') ?>:</b> <?php echo $policyHelp; ?></p>
							   <?php if(!empty($prepaymentHelp)) { ?>
								   <p><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_PREPAYMENT_TITLE') ?>:</b> <?php echo $prepaymentHelp; ?></p>
							   <?php } ?>
							   </div>
							</div>
						</div>
					</div>

<!-- end options  -->
									</td>
                                    <td>
										<?php echo $res->SelectedQt ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <?php if ($res->TotalDiscounted < $res->TotalAmount) { ?>
                                            <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalAmount - $totalAmountPricesExtraIncluded)); ?></span>
                                        <?php } ?>
                                        <?php if ($res->TotalDiscounted > 0) { ?>
                                            <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat(($res->TotalDiscounted - $totalPricesExtraIncluded)); ?></span>

                                        <?php } ?>
											<form action="<?php echo $formRouteDelete ?>" method="POST" style="display: inline-block;"><input type="hidden" name="bfi_CartOrderId" id="bfi_CartOrderId" value="<?php echo $res->CartOrderId ?>" /><input type="hidden" name="bfi_cartId" id="bfi_cartId" value="<?php echo $cartId ?>" /><button class="bfi-btn-delete" data-title="Delete" type="submit" name="remove_order" value="delete" onclick="return confirm('<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_CONFIRMDELETE') ?>')">x</button></form>
									</td>
                                </tr>


								<?php if(!empty($res->PricesExtraIncluded)) { 
									foreach($pricesExtraIncluded as $sdetail) {
										$sdetailName = $sdetail->Name;
										$sdetailName = substr($sdetailName, 0, strrpos($sdetailName, ' - '));
								 ?>	
                                        <tr class="bfi-cart-extra">
                                            <td>
													<div class="bfi-item-title">
														<?php echo  $sdetailName?>
													</div>
													<?php 
													if (!empty($sdetail->CheckInTime) && !empty($sdetail->TimeDuration) && $sdetail->TimeDuration>0)
													{
														$currCheckIn = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
														$currCheckOut = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
														$currDiff = $currCheckOut->diff($currCheckIn);
														$timeDuration = $currDiff->h + ($currDiff->i/60);
//														$startHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
														
														$currCheckIn = new JDate($currCheckIn->format('Y-m-d\TH:i:s')); 
														$currCheckOut = new JDate($currCheckOut->format('Y-m-d\TH:i:s')); 
													?>
														<div class="bfi-timeperiod " >
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?>  <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDuration ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_HOURS') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}
													if (isset($sdetail->TimeSlotId) && $sdetail->TimeSlotId > 0)
													{									
														$currCheckIn = new DateTime(); 
														if($cartId==0){
															$currCheckIn = DateTime::createFromFormat('d/m/Y', $sdetail->TimeSlotDate);
														}else{
															$currCheckIn = new DateTime($sdetail->TimeSlotDate); 
														}
														$currCheckOut = clone $currCheckIn;
														$currCheckIn->setTime(0,0,1);
														$currCheckOut->setTime(0,0,1);
														$currCheckIn->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));

														$currCheckIn = new JDate($currCheckIn->format('Y-m-d\TH:i:s')); 
														$currCheckOut = new JDate($currCheckOut->format('Y-m-d\TH:i:s')); 
														
														$currDiff = $currCheckOut->diff($currCheckIn);

//														$TimeSlotDate = new DateTime($sdetail->TimeSlotDate); 
//														$startHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$endHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$startHour->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));
													?>
														<div class="bfi-timeslot ">
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?>  <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_HOURS') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}

													?>
                                            </td>
                                            <td><!-- paxes --></td>
                                            <td class="text-nowrap"><!-- Unit price -->
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount/$sdetail->CalculatedQt);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted/$sdetail->CalculatedQt );?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-nowrap"> </td>
                                            <td>
												<?php echo $sdetail->CalculatedQt ?>
											</td>
                                            <td class="text-nowrap">
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted );?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                            <?php 
                                    } // foreach $svc
                                } // if res->ExtraServices
								 ?>	
								<?php if(!empty($res->ExtraServices)) { 
									foreach($res->ExtraServices as $sdetail) {					
									$resourceExtraService = $resourceDetail[$sdetail->PriceId]; 
								 ?>	
                                        <tr class="bfi-cart-extra">
                                            <td>
													<div class="bfi-item-title">
														<?php echo  $resourceExtraService->Name ?>
													</div>
													<?php 
													if (!empty($sdetail->CheckInTime) && !empty($sdetail->TimeDuration) && $sdetail->TimeDuration>0)
													{
														$currCheckIn = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
														$currCheckOut = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
														
														$currDiff = $currCheckOut->diff($currCheckIn);
														
														$timeDuration = $currDiff->h + ($currDiff->i/60);
//														$startHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
														
														$currCheckIn = new JDate($currCheckIn->format('Y-m-d\TH:i:s')); 
														$currCheckOut = new JDate($currCheckOut->format('Y-m-d\TH:i:s')); 
													?>
														<div class="bfi-timeperiod " >
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?>  <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $timeDuration ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_HOURS') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}
													if (isset($sdetail->TimeSlotId) && $sdetail->TimeSlotId > 0)
													{									
														$currCheckIn = new DateTime(); 
														if($cartId==0){
															$currCheckIn = DateTime::createFromFormat('d/m/Y', $sdetail->TimeSlotDate);
														}else{
															$currCheckIn = new DateTime($sdetail->TimeSlotDate); 
														}
														$currCheckOut = clone $currCheckIn;
														$currCheckIn->setTime(0,0,1);
														$currCheckOut->setTime(0,0,1);
														$currCheckIn->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
														$currCheckOut->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));

														$currDiff = $currCheckOut->diff($currCheckIn);

														$currCheckIn = new JDate($currCheckIn->format('Y-m-d\TH:i:s')); 
														$currCheckOut = new JDate($currCheckOut->format('Y-m-d\TH:i:s')); 
														

//														$TimeSlotDate = new DateTime($sdetail->TimeSlotDate); 
//														$startHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$endHour = new DateTime("2000-01-01 0:0:00.1"); 
//														$startHour->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
//														$endHour->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));
													?>
														<div class="bfi-timeslot ">
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkin"><?php echo $currCheckIn->format("D") ?> <?php echo $currCheckIn->format("d") ?> <?php echo $currCheckIn->format("M").' '.$currCheckIn->format("Y") ?></span> - <span class="bfi-time-checkin-hours"><?php echo $currCheckIn->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row ">
																<div class="bfi-col-md-3 bfi-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>
																</div>	
																<div class="bfi-col-md-9 bfi-time bfi-text-right"><span class="bfi-time-checkout"><?php echo $currCheckOut->format("D") ?>  <?php echo $currCheckOut->format("d") ?> <?php echo $currCheckOut->format("M").' '.$currCheckOut->format("Y") ?></span> - <span class="bfi-time-checkout-hours"><?php echo $currCheckOut->format('H:i') ?></span>
																</div>	
															</div>	
															<div class="bfi-row">
																<div class="bfi-col-md-3 "><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?>:
																</div>	
																<div class="bfi-col-md-9 bfi-text-right"><span class="bfi-total-duration"><?php echo $currDiff->format('%h') ?></span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_HOURS') ?>
																</div>	
															</div>	
														</div>
													<?php 
													}

													?>
                                            </td>
                                            <td><!-- paxes --></td>
                                            <td class="text-nowrap"><!-- Unit price -->
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount/$sdetail->CalculatedQt);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted/$sdetail->CalculatedQt );?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-nowrap"> </td>
                                            <td>
												<?php echo $sdetail->CalculatedQt ?>
											</td>
                                            <td class="text-nowrap">
                                                <?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
                                                    <span class="text-nowrap bfi_strikethrough  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalAmount);?></span>
                                                <?php } ?>
                                                <?php if($sdetail->TotalDiscounted > 0){ ?>
                                                    <span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted );?></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                            <?php 
								$totalWithVariation +=$sdetail->TotalDiscounted ;
                                    } // foreach $svc
                                } // if res->ExtraServices
							$totalWithVariation +=$res->TotalDiscounted ;
$currStayConfiguration = array("productid"=>$res->ResourceId,"price"=>$res->TotalAmount,"start"=>$currCheckIn->format("Y-m-d H:i:s"),"end"=> $currCheckOut->format("Y-m-d H:i:s"));

$listStayConfigurations[] = $currStayConfiguration;
                            }
                            ?>
<?php 

						
//		} // foreach $itm


	} // foreach $listMerchantsCart

//$totalRequest = BFCHelper::priceFormat($totalWithVariation);
$totalRequest = $totalWithVariation;

?>
		</table>
	</div>	
	<div class=" bfi-border bfi-text-right bfi-pad0-10">
		<span class="text-nowrap bfi-summary-body-resourceprice-total"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS_TOTAL') ?></span>	
		<span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($totalWithVariation);?></span>	
	</div>	

<br />


		<?php
//---------------------FORM


	$current_user = JFactory::getUser();
	$sitename = $this->sitename;

	$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
	$ssllogo = COM_BOOKINGFORCONNECTOR_SSLLOGO;
	$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	$idrecaptcha = uniqid("bfirecaptcha");
	$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	$tmpSearchModel = new stdClass;
	$tmpSearchModel->FromDate = new DateTime();
	$tmpSearchModel->ToDate = new DateTime();
	
//	$routeMerchant = $url_merchant_page . $firstMerchantId.'-'.BFI()->seoUrl($firstMerchantName);
//
//	$routeThanks = $routeMerchant .'/'. _x('thanks', 'Page slug', 'bfi' );
//	$routeThanksKo = $routeMerchant .'/'. _x('errors', 'Page slug', 'bfi' );

$routeThanks = JRoute::_($uriCart.'&layout=thanks');
$routeThanksKo = JRoute::_($uriCart.'&layout=errors');


//$currPolicy = $modelResource->GetMostRestrictivePolicyByIds($listPolicyIdsstr, $language,json_encode($listStayConfigurations));
$bookingTypes = json_decode($currPolicy->MerchantBookingTypesString);
//MerchantBookingTypesString=[{"BookingTypeId":167,"AcquireCreditCardData":false,"Value":null,"Name":null,"Description":"","Data":null,"IsDefault":false,"IsGateway":false,"DeferredPayment":true,"PaymentSystemId":18,"PaymentSystemName":null,"SandboxMode":false,"PaymentSystemRefId":null,"PayOnArrival":false,"DepositRelativeValue":null,"DepositValue":0.0}]
$bookingTypedefault ="";
//$bookingTypesDesc ="";
$bookingTypesoptions = array();
$bookingTypesValues = array();
$bookingTypeFrpmForm = isset($_REQUEST['bookingType'])?$_REQUEST['bookingType']:"";
$bookingTypeIddefault = 0;

if(!empty($bookingTypes)){
	$bookingTypesDescArray = array();
	foreach($bookingTypes as $bt)
	{
		$currDesc = BFCHelper::getLanguage($bt->Name, $language) . "<div class='bfi-ccdescr'>" . BFCHelper::getLanguage($bt->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) . "</div>";
		if($bt->AcquireCreditCardData && !empty($bt->Data)){

			$ccimgages = explode("|", $bt->Data);
			$cCCTypeList = array();
			$currDesc .= "<div class='bfi-ccimages'>";
			foreach($ccimgages as $ccimgage){
				$currDesc .= '<i class="fa fa-cc-' . strtolower($ccimgage) . '" title="'. $ccimgage .'"></i>&nbsp;&nbsp;';
				$cCCTypeList[$ccimgage] = $ccimgage; // JHTML::_('select.option', $ccimgage, $ccimgage);
			}
			$currDesc .= "</div>";
 		}
//		if($bt->AcquireCreditCardData==1 && !BFCHelper::isUnderHTTPS() ){
//			continue;
//		}

		$bookingTypesoptions[$bt->BookingTypeId.":".$bt->AcquireCreditCardData] =  $currDesc;//  JHTML::_('select.option', $bt->BookingTypeId.":".$bt->AcquireCreditCardData, $currDesc );
		$calculatedBookingType = $bt;
		$calculatedBookingType->Deposit = 0;
		
		if(isset($calculatedBookingType->DepositRelativeValue) && !empty($calculatedBookingType->DepositRelativeValue)) {
			if($calculatedBookingType->DepositRelativeValue!='0' && $calculatedBookingType->DepositRelativeValue!='0%' && $calculatedBookingType->DepositRelativeValue!='100%')
			{
				if (strpos($calculatedBookingType->DepositRelativeValue,'%') !== false) {
					$calculatedBookingType->Deposit = (float)str_replace("%","",$calculatedBookingType->DepositRelativeValue) *(float) $totalRequest/100;
				}else{
					$calculatedBookingType->Deposit = $calculatedBookingType->DepositRelativeValue;
				}
			}
			if($calculatedBookingType->DepositRelativeValue==='100%'){
				$calculatedBookingType->Deposit = $totalRequest;
			}
		}

		$bookingTypesValues[$bt->BookingTypeId] = $calculatedBookingType;

		if($bt->IsDefault == true ){
			$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
			$deposit = $calculatedBookingType->Deposit;
			$bookingTypeIddefault = $bt->BookingTypeId;
		}

//		$bookingTypesDescArray[] = BFCHelper::getLanguage($bt->Description, $language);;
	}
//	$bookingTypesDesc = implode("|",$bookingTypesDescArray);

	if(empty($bookingTypedefault)){
		$bt = array_values($bookingTypesValues)[0];
		$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
		$deposit = $bt->Deposit;
		$bookingTypeIddefault = $bt->BookingTypeId;

	}

	if(!empty($bookingTypeFrpmForm)){
			if (array_key_exists($bookingTypeFrpmForm, $bookingTypesValues)) {
				$bt = $bookingTypesValues[$bookingTypeFrpmForm];
				$bookingTypedefault = $bt->BookingTypeId.":".$bt->AcquireCreditCardData;
				$deposit = $bt->Deposit;
				$bookingTypeIddefault = $bt->BookingTypeId;
			}
	}

}

?>
<div class="bfi-payment-form bfi-form-field">
<div class="bf-title-book"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_TITLEFORM') ?></div>
<form method="post" id="bfi-resourcedetailsrequest" class="form-validate" action="<?php echo $formRoute; ?>">
	<div class="bfi-mailalertform">
		<div class="bfi-row">
			<div class="bfi-col-md-6">
			<div class="bfi-clearfix">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME'); ?> *</label>
				<input type="text" value="<?php echo $current_user->name ; ?>" size="50" name="form[Name]" id="Name" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NAME_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME'); ?> *</label>
				<input type="text" value="" size="50" name="form[Surname]" id="Surname" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_SURNAME_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL'); ?> *</label>
				<input type="email" value="<?php echo $current_user->email; ?>" size="50" name="form[Email]" id="formemail" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_CONFIRM'); ?> *</label>
				<input type="email" value="<?php echo $current_user->email; ?>" size="50" name="form[EmailConfirm]" id="formemailconfirm" required equalTo="#formemail" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_EMAIL_CONFIRM_REQUIRED'); ?>">
			</div><!--/span-->
			
						
			<div class="inputaddress" style="display:;">
				<div >
					<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS'); ?> </label>
					<input type="text" value="" size="50" name="form[Address]" id="Address"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ADDRESS_REQUIRED'); ?>">
				</div><!--/span-->
				<div >
					<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP'); ?> </label>
					<input type="text" value="" size="20" name="form[Cap]" id="Cap"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAP_REQUIRED'); ?>">
				</div><!--/span-->
				<div >
					<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NATION'); ?> </label>
					<?php echo JHTML::_('select.genericlist',$cNationList, 'form[Nation]','class="bf_input_select width90percent"','value', 'text', $nation) ?>
				</div><!--/span-->
			</div>
	    </div>
	    <div class="bfi-col-md-6">
			<div >
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_NOTES') ?></label>
				<textarea name="form[note]" class="bfi-col-md-12" style="height:104px;" ></textarea>    
			</div>
			<div >
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE'); ?> *</label>
				<input type="text" value="" size="20" name="form[Phone]" id="Phone" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PHONE_REQUIRED'); ?>">
			</div><!--/span-->
			<div >
				<label><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN_ETA_HOUR') ?></label>
				<select name="form[checkin_eta_hour]" class="bf_input_select" >
					<option value="N.D."><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CHECKIN_ETA_HOUR_NO') ?></option>
					<option value="00.00 - 01.00">00:00 - 01:00</option>
					<option value="01.00 - 02.00">01:00 - 02:00</option>
					<option value="02.00 - 03.00">02:00 - 03:00</option>
					<option value="03.00 - 04.00">03:00 - 04:00</option>
					<option value="04.00 - 05.00">04:00 - 05:00</option>
					<option value="05.00 - 06.00">05:00 - 06:00</option>
					<option value="06.00 - 07.00">06:00 - 07:00</option>
					<option value="07.00 - 08.00">07:00 - 08:00</option>
					<option value="08.00 - 09.00">08:00 - 09:00</option>
					<option value="09.00 - 10.00">09:00 - 10:00</option>
					<option value="10.00 - 11.00">10:00 - 11:00</option>
					<option value="11.00 - 12.00">11:00 - 12:00</option>
					<option value="12.00 - 13.00">12:00 - 13:00</option>
					<option value="13.00 - 14.00">13:00 - 14:00</option>
					<option value="14.00 - 15.00">14:00 - 15:00</option>
					<option value="15.00 - 16.00">15:00 - 16:00</option>
					<option value="16.00 - 17.00">16:00 - 17:00</option>
					<option value="17.00 - 18.00">17:00 - 18:00</option>
					<option value="18.00 - 19.00">18:00 - 19:00</option>
					<option value="19.00 - 20.00">19:00 - 20:00</option>
					<option value="20.00 - 21.00">20:00 - 21:00</option>
					<option value="21.00 - 22.00">21:00 - 22:00</option>
					<option value="22.00 - 23.00">22:00 - 23:00</option>
					<option value="23.00 - 00.00">23:00 - 00:00</option>
					<!-- <option value="00:00 - 01:00 (del giorno dopo)">00:00 - 01:00 (del giorno dopo)</option>
					<option value="01:00 - 02:00 (del giorno dopo)">01:00 - 02:00 (del giorno dopo)</option> -->
				</select>
			</div><!--/span-->
			<div class="bfi-hide">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PASSWORD'); ?> *</label>
				<input type="password" value="<?php echo $current_user->email; ?>" size="50" name="form[Password]" id="Password"   title="">
			</div><!--/span-->
			<div>
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY'); ?> </label>
				<input type="text" value="" size="50" name="form[City]" id="City"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CITY_REQUIRED'); ?>">
			</div><!--/span-->
			<div class="">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA'); ?> </label>
				<input type="text" value="" size="20" name="form[Provincia]" id="Provincia"   title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PROVINCIA_REQUIRED'); ?>">
			</div><!--/span-->
		</div>
	</div>
<!-- VIEW_ORDER_PAYMENTMETHOD -->
		<div class="bfi-paymentoptions" style="display:none;" id="bfi-bookingTypesContainer">
			<h2><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PAYMENTSYSTEM') ?></h2>
			<p><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PAYMENTSYSTEM_DESCR') ?></p>
			<?php  foreach ($bookingTypesoptions as $key => $value) { ?>
				<label for="form[bookingType]<?php echo $key ?>" id="form[bookingType]<?php echo $key ?>-lbl" class="radio">	
					<input type="radio" name="form[bookingType]" id="form[bookingType]<?php echo $key ?>" value="<?php echo $key ?>" <?php echo $bookingTypedefault == $key ? 'checked="checked"' : "";  ?>  ><?php echo $value ?><div class="ccdescr"></div>
				</label>
			<?php } ?>
		</div>
		<div class="bfi-paymentoptions" id="bfi-bookingTypesDescriptionContainer">
			<h2 id="bookingTypeTitle"></h2>
			<span id="bookingTypeDesc"></span>
			<div id="totaldepositrequested" class="bfi-pad0-10" style="display:none;">
				<span class="text-nowrap bfi-summary-body-resourceprice-total"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT') ?></span>	
				<span class="text-nowrap bfi-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"  id="totaldeposit"></span>	
			</div>	
		</div>
		<div class="clear"></div>

<div style="display:none;" id="bfi-ccInformations" class="borderbottom paymentoptions">
		<h2><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCSUBTITLE') ?></h2>
		<div class="bfi-row">   
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCTYPE'); ?> </label>
					<select id="formcc_circuito" name="form[cc_circuito]" class="bfi_input_select">
						<?php 
							foreach($cCCTypeList as $ccCard) {
								?><option value="<?php echo $ccCard ?>"><?php echo $ccCard ?></option><?php 
							}
						?> 
					</select>
			</div>
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNAME'); ?> </label>
				<input type="text" value="" size="50" name="form[cc_titolare]" id="cc_titolare" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
			</div>
		</div>
		<div class="bfi-row bfi-payment-form">
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCNUMBER'); ?> </label>
				<input type="text" value="" size="50" maxlength="50" name="form[cc_numero]" id="cc_numero" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
			</div>
			<div class="bfi-col-md-6">
				<label><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCVALIDATION'); ?></label>
				<div class="bfi-ccdateinput">
						<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYMONTH'); ?></span> <span><input type="text" value="" size="2" maxlength="2" name="form[cc_mese]" id="cc_mese" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>"></span>
						/
						<span><input type="text" value="" size="2" maxlength="2" name="form[cc_anno]" id="cc_anno" required  title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>"></span> <span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CCEXPIRYYEAR'); ?></span>
				</div><!--/span-->
			</div>
		</div>
		<br />
		<div class="bfi-row ">   
			  <div class="bfi-col-md-2">
				 <?php echo $ssllogo ?>
			  </div>
		</div>

</div>	
		<?php if(!empty($currPolicy)) { ?>
<?php 
$policyHelp = "";
$policy = $currPolicy;
if(!empty( $policy )){
	$currValue = $policy->CancellationBaseValue;
	$policyId= $policy->PolicyId;

	switch (true) {
		case strstr($policy->CancellationBaseValue ,'%'):
			$currValue = $policy->CancellationBaseValue;
			break;
		case strstr($policy->CancellationBaseValue ,'d'):
			$currValue = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS'),rtrim($policy->CancellationBaseValue,"d"));
			break;
		case strstr($policy->CancellationBaseValue ,'n'):
			$currValue = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS'),rtrim($policy->CancellationBaseValue,"n"));
			break;
	}
	$currValuebefore = $policy->CancellationValue;
	switch (true) {
		case strstr($policy->CancellationValue ,'%'):
			$currValuebefore = $policy->CancellationValue;
			break;
		case strstr($policy->CancellationValue ,'d'):
			$currValuebefore = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS'),rtrim($policy->CancellationValue,"d"));
			break;
		case strstr($policy->CancellationValue ,'n'):
			$currValuebefore = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_DAYS'),rtrim($policy->CancellationValue,"n"));
			break;
	}
}
?>
			<div class=" bfi-checkbox-wrapper">
					<input name="form[accettazionepolicy]" id="agreepolicy" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
					<label class="bfi-shownextelement"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_POLICY') ?></label>
					<textarea name="form[policy]" class="bfi-col-md-12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php
if (count($allPolicyHelp)>0) {
	foreach ($allPolicyHelp as $key => $value) { 
		echo ($key+1) . ") " . $value . "\r\n";
	}
}
					?>

<?php echo $currPolicy->Description; ?></textarea>
		</div>
		<?php } ?>
		<div class=" bfi-checkbox-wrapper">
				<input name="form[accettazione]" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
				<label class="bfi-shownextelement"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRM') ?></label>
				<textarea name="form[privacy]" class="bfi-col-md-12" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $privacy ?></textarea>    
		</div>
<?php if(!empty($additionalPurpose)) { ?>
		<div class=" bfi-checkbox-wrapper">
				<input name="form[accettazioneadditionalPurpose]" id="agreeadditionalPurpose" aria-invalid="true" aria-required="false" type="checkbox" title="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>">
				<label class="bfi-shownextelement"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CONFIRMADDITIONALPURPOSE') ?></label>
				<textarea name="form[additionalPurpose]" class="" style="display:none;height:200px;margin-top:15px !important;" readonly ><?php echo $additionalPurpose ?></textarea>    
		</div>
<?php } ?>

<?php
JPluginHelper::importPlugin('captcha');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onInit','recaptcha');
$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', 'class=""'));
echo (isset($recaptcha[0])) ? $recaptcha[0] : '';
?>
<div id="recaptcha-error" style="display:none"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_CAPTCHA_REQUIRED') ?></div>

		<input type="hidden" id="actionform" name="actionform" value="<?php echo $formlabel ?>" />
		<input type="hidden" name="form[merchantId]" value="" /> 
		<input type="hidden" id="orderType" name="form[orderType]" value="a" />
		<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language; ?>" />
		<input type="hidden" id="Fax" name="form[Fax]" value="" />
		<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
		<input type="hidden" id="label" name="form[label]" value="<?php echo $formlabel ?>">
		<input type="hidden" id="resourceId" name="form[resourceId]" value="" /> 
		<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks; ?>">
		<input type="hidden" id="redirecterror" name="form[Redirecterror]" value="<?php echo $routeThanksKo;?>" />
		<input type="hidden" id="stayrequest" name="form[stayrequest]" value="<?php //echo $stayrequest ?>">
		<input type="hidden" id="staysuggested" name="form[staysuggested]" value="<?php //echo $staysuggested ?>">
		<input type="hidden" id="isgateway" name="form[isgateway]" value="0" />
		<input type="hidden" name="form[hdnOrderData]" id="hdnOrderData" value='<?php echo $currCart->CartConfiguration ?>' />
		<input type="hidden" name="form[hdnOrderDataCart]" id="hdnOrderDataCart" value='<?php echo $currCart->CartConfiguration ?>' />
		<input type="hidden" name="form[bookingtypeselected]" id="bookingtypeselected" value='<?php echo $bookingTypeIddefault?>' />
		<input type="hidden" id="CartId" name="form[CartId]" value="<?php echo isset($currCart->CartId)?$currCart->CartId:''; ?>">
		<input type="hidden" id="policyId" name="form[policyId]" value="<?php echo $currPolicy->PolicyId?>">

		</div>

		<div class="bfi-row bfi_footer-book" >
			<div class="bfi-col-md-10"></div>
			<div class="bfi-col-md-2 bfi_footer-send"><button type="submit" id="btnbfFormSubmit" class="bfi-btn" style="display:none;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT'); ?></button></div>
		</div>

<?php
$selectedSystemType = array_values(array_filter($bookingTypesValues, function($bt) use($bookingTypedefault) {return $bt->BookingTypeId == $bookingTypedefault;}))
?>
<script type="text/javascript">
<!--
var bookingTypesValues = null;

var completeStay = <?php echo $currCart->CartConfiguration; ?>;
var selectedSystemType = "<?php echo $selectedSystemType[0]->PaymentSystemRefId; ?>";
jQuery(function($)
		{
			jQuery('.bfi-options-help i').webuiPopover({trigger:'hover',placement:'left-bottom',style:'bfi-webuipopover'});

			var svcTotal = 0;
			var allItems = jQuery.makeArray(jQuery.map(jQuery.grep(completeStay.Resources, function(svc) {
				return svc.Tag == "ExtraServices";
			}), function(svc) {
				svcTotal += svc.TotalDiscounted;
				return {
					"id": "" + svc.PriceId + " - Service",
					"name": svc.Name,
					"category": "Services",
					"brand": "<?php //echo $merchant->Name?>",
					"price": (svc.TotalDiscounted / svc.CalculatedQt).toFixed(2),
					"quantity": svc.CalculatedQt
				};
			}));
			/*
			jQuery.each(allItems, function(svc) {
				svcTotal += prc.TotalDiscounted;
			});
			*/
			allItems.push({
				"id": "<?php //echo $resource->ResourceId?> - Resource",
				"name": "<?php //echo $resource->Name?>",
				"category": "<?php //echo $resource->MerchantCategoryName?>",
				"brand": "<?php //echo $merchant->Name?>",
				"variant": completeStay.RefId ? completeStay.RefId.toUpperCase() : "",
				"price": completeStay.TotalDiscounted - svcTotal,
				"quantity": 1
			});
			
			<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
				callAnalyticsEEc("addProduct", allItems, "checkout", "", {
					"step": 1
				});
			<?php endif;?>

			jQuery("#btnbfFormSubmit").show();


			$(".bfi-shownextelement").click(function(){
				$(this).next().toggle();
			});
			
			<?php if(!empty($bookingTypesValues)) : ?>
			bookingTypesValues = <?php echo json_encode($bookingTypesValues) ?>;// don't use quotes
			<?php endif; ?>
			$("#bfi-resourcedetailsrequest").validate(
		    {
				rules: {
					"form[cc_mese]": {
					  required: true,
					  range: [1, 12]
					},
					"form[cc_anno]": {
					  required: true,
					  range: [<?php echo $minyear ?>, <?php echo $maxyear ?>]
					},
					"form[cc_numero]": {
					  required: true,
					  creditcard: true
					},
//					"form[ConfirmEmail]": {
//					  email: true,
//					  required: true,
//					  equalTo: "form[Email]"
//					},
				},
		        messages:
		        {

					"form[cc_mese]": "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>",
		        	"form[cc_anno]": "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>",
		        	"form[cc_numero]": "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED'); ?>",
		        },

				invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        //errorPlacement: function(error, element) { //just nothing, empty  },
//				errorPlacement: function(error, element) {
//					// Append error within linked label
//					$( element )
//						.closest( "form" )
//							.find( "label[for='" + element.attr( "id" ) + "']" )
//								.append( error );
//				},
//				errorElement: "span",
				errorClass: "bfi-error",
				highlight: function(label) {
			    	$(label).removeClass('bfi-error').addClass('bfi-error');
			    	$(label).closest('.control-group').removeClass('bfi-error').addClass('bfi-error');
			    },
			    success: function(label) {
					//label.addClass("valid").text("Ok!");
					$(label).remove();
//					$(label).hide();
					//label.removeClass('error');
					//label.closest('.control-group').removeClass('error');
			    },
				submitHandler: function(form) {
					var $form = $(form);
					if($form.valid()){
						if (typeof grecaptcha === 'object') {
							var response = grecaptcha.getResponse(window.bfirecaptcha['<?php echo $idrecaptcha ?>']);
							//recaptcha failed validation
							if(response.length == 0) {
								$('#recaptcha-error-<?php echo $idrecaptcha ?>').show();
								return false;
							}
							//recaptcha passed validation
							else {
								$('#recaptcha-error-<?php echo $idrecaptcha ?>').hide();
							}					 
						}
						bookingfor.waitBlockUI();
//						jQuery.blockUI({message: ''});
						if ($form.data('submitted') === true) {
							 return false;
						} else {
							// Mark it so that the next submit can be ignored
							$form.data('submitted', true);
							var svcTotal = 0;
						
							<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
							callAnalyticsEEc("addProduct", allItems, "checkout", "", {
								"step": 2,
							});
							
							callAnalyticsEEc("addProduct", allItems, "checkout_option", "", {
								"step": 2,
								"option": selectedSystemType
							});
							<?php endif; ?>
							form.submit();
						}					}

				}

			});
			$("input[name='form[bookingType]']").change(function(){
				var currentSelected = $(this).val().split(':')[0];
				selectedSystemType = Object.keys(bookingTypesValues).indexOf(currentSelected) > -1 ? bookingTypesValues[currentSelected].PaymentSystemRefId : "";
				checkBT();
			});
			var bookingTypeVal= $("input[name='form[bookingType]']");
			var container = $('#bfi-bookingTypesContainer');
			if(bookingTypeVal.length>1 && container.length>0){
					container.show();
			}
			function checkBT(){
					var ccInfo = $('#bfi-ccInformations');
					if (ccInfo.length>0) {
						try
						{
							var currCC = $("input[name='form[bookingType]']:checked");
							if (!currCC.length) {
								currCC = $("input[name='BookingType']")[0];
								$(currCC).prop("checked", true);
							}
							var cc = $(currCC).val();
							var ccVal = cc.split(":");
							var reqCC = ccVal[1];
							if (reqCC) { 
								ccInfo.show();
							} else {
								ccInfo.hide();
							}
							var idBT = ccVal[0];
							$("#bookingtypeselected").val(idBT);

							$.each(bookingTypesValues, function(key, value) {
								if(idBT == value.BookingTypeId){
//									$("#bookingTypeTitle").html(value.Name);
//									$("#bookingTypeDesc").html(value.Description);
									if(value.Deposit!=null && value.Deposit!='0'){
										
										$("#totaldepositrequested").show();
										$("#totaldeposit").html(bookingfor.priceFormat(value.Deposit, 2, '.', ''));

//										$("#bf-summary-footer-deposit").show();
//										$("#footer-deposit").html(value.Deposit);

										$("#isgateway").val("0");
										if(value.DeferredPayment=='0' || value.DeferredPayment==false){
											$("#isgateway").val(value.IsGateway ? "1" : "0");
										}
										return false;
									}else{
										$("#isgateway").val("0");
										$("#totaldepositrequested").hide();
									}
								}
							});
							
						}
						catch (err)
						{
						}

					}
			}
			checkBT();

		});

var bfisrv = [];
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
var listServiceIds = "<?php echo implode(",", $allServiceIds) ?>";
var bfisrvloaded=false;
var resGrp = [];
var loadedResGrp=false;

function getAjaxInformationsResGrp(){
	if (!loadedResGrp)
	{
		loadedResGrp=true;
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
							resGrp[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
						} else {
							if (val.IconSrc != null && val.IconSrc != '') {
								resGrp[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
							}
						}
					});	
					bfiUpdateInfoResGrp();
				}
				jQuery('[data-toggle="tooltip"]').tooltip({
					position : { my: 'center bottom', at: 'center top-10' },
					tooltipClass: 'bfi-tooltip bfi-tooltip-top '
				}); 

		},'json');
	}
}
function bfiUpdateInfoResGrp(){
	jQuery(".bfiresourcegroups").each(function(){
		var currList = jQuery(this).attr("rel");
		if (currList!= null && currList!= '')
		{
			var srvlist = currList.split(',');
			var srvArr = [];
			jQuery.each(srvlist, function(key, srvid) {
				if(typeof resGrp[srvid] !== 'undefined' ){
					srvArr.push(resGrp[srvid]);
				}
			});
			jQuery(this).html(srvArr.join(" "));
		}

	});
}


function getAjaxInformationsSrv(){
	if (!bfisrvloaded)
	{
		bfisrvloaded=true;
		if(listServiceIds!=""){
			var querySrv = "task=GetServicesByIds&ids=" + listServiceIds + "&language=<?php echo $language ?>";
			jQuery.post(bfi_variable.bfi_urlCheck, querySrv, function(data) {
				if(data!=null){
					jQuery.each(data, function(key, val) {
						bfisrv[val.ServiceId] = val.Name ;
					});	
					bfiUpdateInfo();
				}
			},'json');
		}
	}
}

function bfiUpdateInfo(){
	jQuery(".bfisimpleservices").each(function(){
		var currList = jQuery(this).attr("rel");
		if (currList!= null && currList!= '')
		{
			var srvlist = currList.split(',');
			var srvArr = [];
			jQuery.each(srvlist, function(key, srvid) {
				if(typeof bfisrv[srvid] !== 'undefined' ){
					srvArr.push(bfisrv[srvid]);
				}
			});
			jQuery(this).html(srvArr.join(", "));
		}

	});
}

jQuery(document).ready(function () {
	getAjaxInformationsSrv();
	getAjaxInformationsResGrp();
});
	//-->
	</script>	
</form>
</div>		
</div>		
		<?php 
				
		
		}

}else{
	echo __('Cart Not enabled! ', 'bfi');
}

?>   
<!-- {emailcloak=off} -->

