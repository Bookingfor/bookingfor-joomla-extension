<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


//$merchant = $resource->Merchant;
$currencyclass = "eur";

$OrderDetail = BFCHelper::getState('currentOrder', 'bfi');
if(empty($order)){
	$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
	$OrderDetail = BFCHelper::calculateOrder($OrderJson,$language);
//	BFI()->currentOrder = $order;
	BFCHelper::setState($OrderDetail, 'currentOrder', 'bfi');
}

$showCheckout = false;
if (count($OrderDetail->Resources) > 0)
{
	$AnyResources = array_filter($OrderDetail->Resources, function($o) {
								return $o->TimeSlotId == null || $o->CheckInTime == null;
							});
	$showCheckout = count($AnyResources) > 0 ;
}

$nad = $OrderDetail->SearchModel->AdultCount;
$nch = $OrderDetail->SearchModel->ChildrenCount;
$nse =  $OrderDetail->SearchModel->SeniorCount;
$countPaxes = $nad + $nch + $nse;
$nchs = array($OrderDetail->SearchModel->childages1,$OrderDetail->SearchModel->childages2,$OrderDetail->SearchModel->childages3,$OrderDetail->SearchModel->childages4,$OrderDetail->SearchModel->childages5,null);
//$nchs = array_filter($nchs);
$nchs = array_slice($nchs,0,$nch);
//
setlocale(LC_TIME, $language);
?>
<!-- Summary dx -->
	<div class="bf-summary">
		<div class="bf-summary-logo"><a href="<?php echo $route?>"><img  src="<?php echo $merchantLogo; ?>"></a></div>
<!-- Summary header -->
		<div class="bf-summary-header">
			<div class="bf-summary-title"><i class="fa fa-suitcase"></i>&nbsp;<?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SUMMARY_TITLE') ?></div>
			<div class="bf-summary-header-name"><a href="<?php echo $route?>"><?php echo  $merchant->Name?></a></div>
			<div class="bf-summary-header-rating">
			<?php for($i = 0; $i < $merchant->Rating ; $i++) { ?>
			  <i class="fa fa-star"></i>
			<?php } ?>
			</div>

			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-calendar" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>: <?php echo $OrderDetail->SearchModel->FromDate->format('d/m/Y') ?></div>
			</div>
			<?php  if ($showCheckout) : ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1"><i class="fa fa-calendar" aria-hidden="true"></i></div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>: <?php echo $OrderDetail->SearchModel->ToDate->format('d/m/Y') ?></div>
			</div>
			<?php  endif ?>

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
<?php if (count($OrderDetail->Resources)> 0):?>			
<?php 	
	$rdetailgrouped = array();
	foreach ($OrderDetail->Resources as $data) {
		$id = $data->BookingType;
		if (isset($rdetailgrouped[$id])) {
			$rdetailgrouped[$id][] = $data;
		} else {
			$rdetailgrouped[$id] = array($data);
		}
	}

	foreach ($rdetailgrouped as $key=>$rdetailgroup) // foreach $rdetail
	{


?>
<?php 	
	$resCount = 0;
	$totalAmount = 0;
	$totalDiscounted = 0;
	
	foreach ($rdetailgroup as $rdetail) // foreach $rdetail
	{
?>
		
		<div class="bf-bBookingType bf-bBookingType<?php echo $key ?>" style="display:none;">
			<div class="bf-summary-body" >
				<div class="bf-summary-body-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_RESOURCES') ?>:</div>
				<div class="bf-summary-body-resourcename">
					<?php echo $rdetail->Name ?>
							<?php if ($rdetail->TimeSlotId > 0)
							{
								$startHour = new DateTime("2000-01-01 0:0:00.1"); 
								$endHour = new DateTime("2000-01-01 0:0:00.1"); 
								$startHour->add(new DateInterval('PT' . $rdetail->TimeSlotStart . 'M'));
								$endHour->add(new DateInterval('PT' . $rdetail->TimeSlotEnd . 'M'));

							?>
								(<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
							<?php 
							}
							if ($rdetail->RatePlanId > 0 && !($rdetail->AvailabilityType == 2 || $rdetail->AvailabilityType == 3))
							{ ?>
								<br />
							   <?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TREATMENT') ?>: <?php echo BFCHelper::getLanguage($rdetail->RatePlanName, $language) ?>
							<?php 
							}
							if (!empty($rdetail->CheckInTime) && !empty($rdetail->TimeDuration))
							{
								$startHour = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
								$endHour = DateTime::createFromFormat("YmdHis", $resource->CheckInTime);
								$endHour->add(new DateInterval('PT' . $rdetail->TimeDuration . 'M'));
							?>
								(<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
							<?php 
							}
							?>
				</div>
				<div class="bf-summary-body-resourceprice">
					<?php 
					if($rdetail->PercentVariation){ ?>
						<div class="specialoffer variationlabel " rel="<?php echo $rdetail->AllVariations ?>" rel1="<?php echo  $rdetail->ResourceId ?>_<?php echo $resCount ?>" >
							<span class="variationlabel_percent"><?php echo $rdetail->PercentVariation ?></span>% <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
						</div><br />
					<?php 
					}
					if($rdetail->TotalDiscounted < $rdetail->TotalAmount){ ?>
						<span class="bf-summary-body-resourceprice-total-strike bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($rdetail->TotalAmount); ?></span>
					<?php 
					}
					if($rdetail->TotalDiscounted > 0){ ?>
						<span class="bf-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($rdetail->TotalDiscounted); ?></span>
					<?php 
					}
					?>
				</div>
				<div class="divoffers" id="divoffers<?php echo  $rdetail->ResourceId ?>_<?php echo $resCount ?>" style="display:none;">
						<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
						<span class="sr-only">Loading...</span>
				</div>
				<?php if(!empty($rdetail->ExtraServices)){ ?>
					<hr />
					<div class="bf-summary-body-title"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?>:</div>
					<?php foreach($rdetail->ExtraServices as $sdetail):?>						
							<div class="bf-summary-body-resourcename">
								<strong><?php echo $sdetail->Name ?> (<?php echo $sdetail->CalculatedQt ?>)</strong>
								<?php 
								if ($sdetail->TimeSlotId > 0)
								{									
									$TimeSlotDate = DateTime::createFromFormat('d/m/Y', $sdetail->TimeSlotDate);
									$startHour = new DateTime("2000-01-01 0:0:00.1"); 
									$endHour = new DateTime("2000-01-01 0:0:00.1"); 
									$startHour->add(new DateInterval('PT' . $sdetail->TimeSlotStart . 'M'));
									$endHour->add(new DateInterval('PT' . $sdetail->TimeSlotEnd . 'M'));
								?>
									<?php echo $TimeSlotDate->format('d/m/Y') ?> (<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)
								<?php 
								}
								if (!empty($sdetail->CheckInTime) && !empty($sdetail->TimeDuration) && $sdetail->TimeDuration>0)
								{
									$startHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
									$endHour = DateTime::createFromFormat("YmdHis", $sdetail->CheckInTime);
									$endHour->add(new DateInterval('PT' . $sdetail->TimeDuration . 'M'));
								?>
									<?php //echo $startHour->format('d/m/Y') ?> (<?php //echo  $startHour->format('H:i') ?> - <?php //echo  $endHour->format('H:i') ?>)
								<?php 
								}
								?>
							</div>
							<div class="bf-summary-body-resourceprice">
								<?php if($sdetail->TotalDiscounted < $sdetail->TotalAmount){ ?>
									<span class="bf-summary-body-resourceprice-total-strike bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($sdetail->TotalAmount);?></span>
								<?php } ?>
								<span class="bf-summary-body-resourceprice-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($sdetail->TotalDiscounted );?></span>
							</div>
							<?php 	
									$totalAmount += $sdetail->TotalAmount;
									$totalDiscounted += $sdetail->TotalDiscounted;
							?>

					<?php endforeach;?>
				<?php } ?>
			</div>
		</div>
		<div class="bf-summary-footer">
			<div class="bf-summary-footer-title"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_TOTALPRICE') ?>:</div>
			<?php if($rdetail->TotalAmount > $rdetail->TotalDiscounted) { ?>
				<div class="com_bookingforconnector_merchantdetails-resource-stay-price originalquote">
					<span class="com_bookingforconnector_resourcelist_strikethrough notvars">
						<span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight bfi_<?php echo $currencyclass ?>"> <?php echo  BFCHelper::priceFormat($rdetail->TotalAmount) ?></span>
					</span>
				</div>
			<?php } ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 bf-summary-footer-totalprice bfi_<?php echo $currencyclass ?>">
					<?php echo BFCHelper::priceFormat($rdetail->TotalDiscounted); ?>
				</div>
			</div>
		</div>
<?php 	
		$totalAmount += $rdetail->TotalAmount;
		$totalDiscounted += $rdetail->TotalDiscounted;
		}// end foreach $rdetail
?>
		<div class="bf-summary-footer bf-summary-footer<?php echo $key ?>">
			<div class="bf-summary-footer-title"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_TOTALPRICE') ?>:</div>
			<?php if($totalAmount> $totalDiscounted) { ?>
				<div class="com_bookingforconnector_merchantdetails-resource-stay-price originalquote">
					<span class="com_bookingforconnector_resourcelist_strikethrough notvars">
						<span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight bfi_<?php echo $currencyclass ?>"> <?php echo  BFCHelper::priceFormat($totalAmount) ?></span>
					</span>
				</div>
			<?php } ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 bf-summary-footer-totalprice bfi_<?php echo $currencyclass ?>">
					<?php echo BFCHelper::priceFormat($totalDiscounted); ?>
				</div>
			</div>

			<div id="totaldepositrequested<?php echo $key ?>" class="bf-summary-footer-deposit" style="margin-top:5px;display:none;">
				<div class="bf-summary-footer-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_DEPOSIT') ?>:</div>
				<div class="bf-summary-footer-totalprice bfi_<?php echo $currencyclass ?>" id="footer-deposit<?php echo $key ?>"></div>
			</div>
		</div>
<?php 			
		}// end foreach $BookingType
?>
	<?php endif;?>		
</div>

<script type="text/javascript">
<!--
	var offersLoaded = [];

	jQuery(function($)
		{
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
					name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
					$html += '<p class="title">' + name + '</p>';
					descr = bookingfor.nl2br(jQuery("<p>" + descr + "</p>").text());
					$html += '<p class="description ">' + descr + '</p>';
				});
				offersLoaded[discountIds] = $html;
				fn(obj,$html);
				//jQuery(obj).html($html);
		},'json');
}

//-->
</script>