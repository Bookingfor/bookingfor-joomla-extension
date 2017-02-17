<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';
$debugmode = false;

$config = $this->config;
$isportal = $config->get('isportal', 1);
$usessl = $config->get('usessl', 0);

$resource = $this->item;

if(isset($resource->UnitId)){
	$resource->ResourceId = $resource->UnitId;
}
$extras = $this->Extras;
$priceTypes = $this->PriceTypes;
$currentState = $this->params['state'];
$refreshState = isset($this->params['refreshcalc']);
$completestay = null;
$stay = null;

if(isset($this->stay)) {
	$completestay = $this->stay;
	$stay = $completestay->SuggestedStay;
}

$calPrices = null;
$calPkgs = null;

$stayAvailability = 0;

$selPriceType = null;
$selPriceTags = '';

if(isset($stay->Availability)){
	$stayAvailability = $stay->Availability;
}

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
//TODO: cosa farne dei pacchetti nel calcolo singolo della risorsa?
$packages = null;
/*
if(isset($stay->CalculatedPackages)){
	$calPkgs = $stay->CalculatedPackages;
}
*/

$merchant = $resource->Merchant;
//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$merchantLogo = JURI::base() ."/media/com_bookingfor/images/default.png";

if ($merchant!= null && $merchant->LogoUrl != '') {
		$merchantLogo = BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl ,'medium');
}

$checkoutspan = '+1 day';
$startDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDateByMerchantId($resource->MerchantId));
$endDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getEndDateByMerchantId($resource->MerchantId));
$startDate2 = clone $startDate;
$startDate2->modify($checkoutspan);

//$format = BFCHelper::getVar('format');
//$tmpl = BFCHelper::getVar('tmpl');
//$formRoute = JRoute::_('index.php?option=com_bookingforconnector&format='.$format.'&tmpl='.$tmpl.'&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
$formRoute = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
if($usessl){
	$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,1);
}

$gotCalculator = BFCHelper::getBool('calculate');
$checkin = $this->params['checkin'];
$checkout = $this->params['checkout'];
$paxages = $this->params['paxages'];
$selExtras = explode('|',$this->params['extras']);
$selPackages = explode('|',$this->params['packages']);
$selVariationId = $this->params['variationPlanId'];


$viewSummary = !empty($this->params['fromExtForm']) ?: false || $currentState =='optionalPackages' || $currentState =='booking';
$viewExtra = $currentState =='optionalPackages';
$viewForm = !empty($this->params['fromExtForm']) ?: false || $currentState =='booking';


$selPriceTypeObj = null;

if (count($priceTypes) > 0){
foreach ($priceTypes as $pt) {
	if ($pt->Type != $selPriceType)  { 
		continue;
	}
	$selPriceTypeObj = $pt;
	break;
}

}

$singleRateplan = $completestay;

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

$dateStringCheckin =  $checkin->format('d/m/Y');
$dateStringCheckout =  $checkout->format('d/m/Y');

$dateStayCheckin = new DateTime();
$dateStayCheckout = new DateTime();
$DiscountedPrice = 0;
$total = 0;

if(!empty($stay)){
	if(!empty($stay->CheckIn)){
$dateStayCheckin = BFCHelper::parseJsonDate($stay->CheckIn);
	}
	if(!empty($stay->CheckOut)){
$dateStayCheckout = BFCHelper::parseJsonDate($stay->CheckOut);
	}
	$total = (float)$stay->TotalPrice;
	if(!empty($stay->DiscountedPrice)){
		$DiscountedPrice = (float)$stay->DiscountedPrice;
	}
}

$totalPerson = $nad + $nch + $nse;
//$showQuote = ($stay->DiscountedPrice > 0 || $stayAvailability > 0) && ( ($nad + $nch) <= $resource->MaxCapacityPaxes && ($nad + $nch) >= $resource->MinCapacityPaxes );
$showQuote = ($DiscountedPrice > 0 && $stayAvailability > 0) && ( ($totalPerson) <= $resource->MaxCapacityPaxes && ($totalPerson) >= $resource->MinCapacityPaxes )  && $dateStayCheckin == $dateStringCheckin && $dateStayCheckout == $dateStringCheckout;

$totalDiscounted = $DiscountedPrice;
$totalWithVariation = $this->totalWithVariation;
?>
<br />
<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
<?php if ($showQuote):?>
<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9 com_bookingforconnector_resource-payment-form">
  <div class="com_bookingforconnector_resource-calculator-requestForm" style="display:<?php echo $viewForm ? 'block;' : 'none;'; ?>" >
	<?php echo  $this->loadTemplate('inforequest');?>
	<br /><br />
  </div>
</div>
<?php endif;?>		
<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 com_bookingforconnector_resource-calculator">
<img  src="<?php echo $merchantLogo; ?>">	
<br />&nbsp;	
	
	
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 padding1020 borderright nopadding">
		<div class="com_bookingforconnector_resource-calculatorTable <?php echo $viewExtra ? 'com_bookingforconnector_resource-calculatorTable-viewextras' : ''; ?> <?php echo $viewSummary ? 'com_bookingforconnector_resource-calculatorTable-viewsummary' : ''; ?>">
		<!-- summary -->
				<div class="com_bookingforconnector_resource-summary">
					<h4 class="summarytitle"><i class="fa fa-suitcase"></i><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SUMMARY_TITLE') ?></h4>

					<strong><?php echo $resourceName ?></strong>
					<p>
					<?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>: <?php echo $checkin->format('d/m/Y') ?> - <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>: <?php echo $checkout->format('d/m/Y') ?><br/>
					<?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ADULTS') ?>: <?php echo $nad ?> 
						<?php if ($nch > 0): ?>
						| <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDREN') ?>: <?php echo $nch ?> - <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDRENAGE') ?>: <?php echo implode(', ',$nchs) ?>
						<?php endif; ?>
						<?php if ($nse > 0): ?>
						| <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SENIORES') ?>: <?php echo $nse ?>
						<?php endif; ?>
					<?php if ($selPriceTypeObj != null): ?>
						<br/>
						<?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TREATMENT') ?>: <?php echo BFCHelper::getLanguage($selPriceTypeObj->Name, $this->language) ?>
						<?php $description = BFCHelper::getLanguage($singleRateplan->Description, $this->language); ?>
						<?php if ($description!=""): ?>				
							<br/>
							<?php echo $description ?>
							<br />
						<?php endif; ?>
					<?php endif; ?>
					<?php if (count($calPrices) > 0): ?>
					<br/>
					<?php $countPrice = 0; ?>
							<span class="commaseparator">
							<?php foreach($calPrices as $calPrice):?>
								<?php if($calPrice->Tag<>"person" && $calPrice->Tag<>"" ) :?>
									<?php if($countPrice >0) :?>, <?php endif; ?><?php echo BFCHelper::getLanguage($calPrice->Name, $this->language) ?>:  <?php echo $calPrice->CalculatedQt ?>
									<?php $countPrice++; ?>
								<?php endif;?>
							<?php endforeach;?>
							</span>
							<?php if(isset($calPkgs)) : ?>
							<?php foreach($calPkgs as $calPkg):?>
								<?php 
								$pkg = null;
								if(isset($packages)) {
								foreach($packages as $package) {
									if ($package->PackageId == $calPkg->PackageId) {
										$pkg = $package;
										break;
									}
								} 
								}
								?>
								<?php echo BFCHelper::getLanguage($pkg->Name, $this->language) ?>: 
								<?php foreach($calPkg->CalculatedPricesDetails as $calPrice):?>
									<?php if($calPrice->Tag!="person") :?>
										<?php if($countPrice >0) :?>, <?php endif; ?><?php echo BFCHelper::getLanguage($calPrice->Name, $this->language) ?>:  <?php echo $calPrice->CalculatedQt ?>
									<?php $countPrice++; ?>
									<?php endif;?>
								<?php endforeach;?>
						<?php endforeach;?>
						<?php endif;?>
										<?php if($debugmode) :?>				
						<!-- elenco prezzi completi -->
											<br />
											<table>
												<?php foreach($calPrices as $calPrice):?>
												<tr>
													<td><?php echo BFCHelper::getLanguage($calPrice->Name, $this->language) ?> </td>
													<td>x <?php echo $calPrice->CalculatedQt ?> </td>
													<td>= <?php echo BFCHelper::priceFormat($calPrice->TotalAmount) ?> </td>
													<td><?php echo BFCHelper::priceFormat($calPrice->TotalVariationAmount) ?> </td>
													<td><?php echo BFCHelper::priceFormat($calPrice->TotalAmount + $calPrice->TotalVariationAmount) ?> </td>					
												</tr>
												<?php endforeach;?>
												<?php foreach($calPkgs as $calPkg):?>
													<?php 
													$pkg = null;
													foreach($packages as $package) {
														if ($package->PackageId == $calPkg->PackageId) {
															$pkg = $package;
															break;
														}
													} 
													?>
													<tr>
														<td><?php echo BFCHelper::getLanguage($pkg->Name, $this->language) ?> </td>
														<td colspan="100%">&nbsp; </td>
													</tr>
													<?php foreach($calPkg->CalculatedPricesDetails as $calPrice):?>
													<tr>
														<td><?php echo BFCHelper::getLanguage($calPrice->Name, $this->language) ?> </td>
														<td>x <?php echo $calPrice->CalculatedQt ?> </td>
														<td>= <?php echo BFCHelper::priceFormat($calPrice->TotalAmount) ?> </td>
														<td><?php echo BFCHelper::priceFormat($calPrice->TotalVariationAmount) ?> </td>
														<td><?php echo BFCHelper::priceFormat($calPrice->TotalAmount + $calPrice->TotalVariationAmount) ?> </td>					
													</tr>
													<?php endforeach;?>
												<?php endforeach;?>
											</table>
										<?php endif; ?>
					<?php endif; ?>
					</p>
				</div>
		</div>	
<!-- end  summary -->	
	</div>

	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 nopadding com_bookingforconnector_resource-calculatorTable <?php echo $viewExtra ? 'com_bookingforconnector_resource-calculatorTable-viewextras' : ''; ?> <?php echo $viewSummary ? 'com_bookingforconnector_resource-calculatorTable-viewsummary' : ''; ?>">
<!-- Quotazione -->
		<?php if ($showQuote):?>
			<div id="resourceSummary" class="com_bookingforconnector_resource-summary">
				<h4><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_QUOTATION') ?></h4>
				<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
					<div >
						<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),$stay->Days) ?>
						<?php if (!empty($completestay->Discount)):?>
						<div>
							<?php echo  BFCHelper::getLanguage($completestay->Name, $this->language) ?>
						</div>
						<?php endif;?>
						<?php if (!empty($this->stay->Supplement)):?>
						<div>
							<?php echo  BFCHelper::getLanguage($stay->Supplement->Name, $this->language) ?>
						</div>
						<?php endif;?>							
					</div>
																						
						<?php if ($currentState != 'booking'):?>
							<div class="com_bookingforconnector_merchantdetails-resource-stay-price" style="<?php echo $currentState != 'booking' ? 'display:block;' : 'display:none;'; ?>">
								<?php if ($totalDiscounted < $total): ?>
									<span class="com_bookingforconnector_strikethrough">&euro; <?php echo  BFCHelper::priceFormat($total) ?></span>
								<?php endif; ?>
								&euro; <span class="com_bookingforconnector_merchantdetails-resource-stay-total"><?php echo  BFCHelper::priceFormat($totalDiscounted) ?></span>
<!-- Botton prenota -->	
								<br /><br />
								<a class="btn btn-info" href="javascript:changeVariation(jQuery, '');"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_BOOK') ?> </a>
								<a class="btn" href="javascript:changeBooking(jQuery);"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHANGE') ?> </a>
							</div>

							<?php if (isset($completestay->Variations) && count($completestay->Variations) > 0):?>
								<div class="com_bookingforconnector_merchantdetails-resource-stay-variations" style="<?php echo $currentState != 'booking' ? 'display:block;' : 'display:none;'; ?>">
									<br />
									<?php foreach($this->stay->Variations as $variation):?>
										<div class="com_bookingforconnector_merchantdetails-resource-stay-price">
											<?php $totalVar = $variation->TotalAmount + $variation->TotalPackagesAmount;?>
											<?php echo  BFCHelper::getLanguage($variation->Name, $this->language) ?> <br />
											&euro; <span class="com_bookingforconnector_merchantdetails-resource-stay-total"><?php echo  BFCHelper::priceFormat($total + $totalVar) ?></span>
											<br />
											<a class="btn btn-info" href="javascript:changeVariation(jQuery, '<?php echo  $variation->VariationPlanId; ?>');"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_BOOK') ?> </a>
										</div>
									<?php endforeach;?>
								</div>
							<?php endif; ?>
						<?php else:?>
																												<?php if ($selVariationId != ''):?>
																													<div class="com_bookingforconnector_merchantdetails-resource-stay-variations" style="<?php echo $currentState == 'booking' ? 'display:block;' : 'display:none;'; ?>">
																														<?php foreach($this->stay->Variations as $variation):?>
																															<?php if ($variation->VariationPlanId == $selVariationId):?>
																																<div class="com_bookingforconnector_merchantdetails-resource-stay-price">
																																	<?php echo  BFCHelper::getLanguage($variation->Name, $this->language) ?> <br />
																																	&euro; <span class="com_bookingforconnector_merchantdetails-resource-stay-total"><?php echo  BFCHelper::priceFormat($totalWithVariation) ?></span>
																																</div>
																																<?php break;?>
																															<?php endif;?>
																														<?php endforeach;?>
																													</div>
																												<?php else :?>
																													<div class="com_bookingforconnector-gray-highlight">
																														<span class="com_bookingforconnector-item-secondary-stay-total">&euro; <?php echo  BFCHelper::priceFormat($totalDiscounted) ?></span>
																														<?php if ($totalDiscounted < $total): ?>
																															<span class="com_bookingforconnector_strikethrough">&euro; <?php echo  BFCHelper::priceFormat($total) ?></span>
																														<?php endif; ?>
																													</div>
																													<div class="clearboth"></div>
																												<?php endif;?>						
						<?php endif; ?>
					</div>
				<div class="clear"></div>
			</div>
		<?php endif;?>
<!-- bottone ricalcola -->		
		<div id="calculateButton" class="com_bookingforconnector_resource-calculatorForm-button" <?php if ($showQuote):?>style="display:none;"<?php endif;?>>
			<?php if ($currentState != 'booking'):?>
				<a class="btn btn-warning" href="javascript:calculateQuote()"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CALCULATE') ?> </a>
			<?php else:?>
				<a class="btn btn-warning" href="<?php echo $formRoute?>"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CALCULATE') ?> </a>
			<?php endif;?>
		</div>
	</div>
</div>					
</div>
