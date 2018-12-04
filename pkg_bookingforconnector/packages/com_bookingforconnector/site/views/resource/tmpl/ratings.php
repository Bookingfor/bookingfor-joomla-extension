<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// No direct access to this file
jimport('joomla.utilities.date');

$resource = $this->item;
$resource_id = $resource->ResourceId; //per form contactpopup
$merchant = $resource->Merchant;
$language = $this->language;

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

$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 
$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('nobr'=>'nobr', 'striptags'=>'striptags')); 

$summaryRatings = null;

if ($merchant->RatingsContext != NULL && $merchant->RatingsContext > 0) {
	$showReview = true;
	if ($merchant->RatingsContext ==1 && !empty($merchant->Avg)) {
		$reviewavg =  isset($merchant->Avg) ? $merchant->Avg->Average : 0;
		$reviewcount =  isset($merchant->Avg) ? $merchant->Avg->Count : 0;
		$summaryRatings = $merchant->Avg;
	}
	if ($merchant->RatingsContext ==2 || $merchant->RatingsContext ==3 ) {
		$summaryRatings = BFCHelper::getResourceRatingAverage($merchant->MerchantId,$resource->ResourceId);
		if(!empty($summaryRatings)){
			$reviewavg = $summaryRatings->Average;
			$reviewcount = $summaryRatings->Count;
		}
	}
}
		$ratings = null;
		if(!empty( $reviewcount )){
			if ($merchant->RatingsContext ==1){
	//			$modelmerchant =  new BookingForConnectorModelMerchantDetails;
//				$summaryRatings = BFCHelper::getRatingByMerchantId($merchant->MerchantId);
				$ratings = BFCHelper::getMerchantRatings(0,5,$merchant->MerchantId);
			}else{
	//			$summaryRatings = $model->getRatingAverageFromService($merchant->MerchantId,$resource->ResourceId);
				$ratings = BFCHelper::getResourceRatings(0,5,$resource->ResourceId);
			}
			
		}
//$db   = JFactory::getDBO();
//$uri  = 'index.php?option=com_bookingforconnector&view=resource';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
////$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//$itemId = intval($db->loadResult());
//$itemIdMerchant=0;
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//if($isportal){
//	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//	$itemIdMerchant = intval($db->loadResult());
//}
//if($itemId == 0){
//	$itemId = $itemIdMerchant;
//}
//if ($itemId<>0){
//	$currUriresource.='&Itemid='.$itemId;
//}
$uri = COM_BOOKINGFORCONNECTOR_URIRESOURCE;
$uriMerchant  = COM_BOOKINGFORCONNECTOR_URIMERCHANTDETAILS;
$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);

$resourceRoute = JRoute::_($currUriresource);
$routeRating = JRoute::_($currUriresource.'&layout=rating');				

$currUriMerchant = $uriMerchant. '&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName);
//if ($itemIdMerchant<>0){
//	$currUriMerchant.= '&Itemid='.$itemIdMerchant;
//}
$routeMerchant = JRoute::_($currUriMerchant,true, -1);

?>
<div >
	<?php
	// preparo la lista per i filtri..
	$list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
	$listfiltered = BFCHelper::getSession('ratingsfilterstypologyid', 0 , 'com_bookingforconnector');
	$genericlist = JHTML::_('select.genericlist', $list, 'filters[typologyid]',array('onchange' => 'this.form.submit();') , 'value', 'text', $listfiltered);

	if(isset($summaryRatings)) {
		$val1 = round($summaryRatings->AValue1 * 10);
		$val2 = round($summaryRatings->AValue2 * 10);
		$val3 = round($summaryRatings->AValue3 * 10);
		$val4 = round($summaryRatings->AValue4 * 10);
		$val5 = round($summaryRatings->AValue5 * 10);
		$total = number_format((float)$summaryRatings->Average, 1, '.', '');

		$totalInt = BFCHelper::convertTotal($total);
	}
	?>
	<br /><br />
	<?php if (isset($summaryRatings)){?>
	<div class="bfi-rating-container">
		<div class="bfi-row">
			<div class="bfi-col-md-3 bfi-text-center">
				<div class="bfi-rating_valuation">
					<div class="bfi-rating-title"><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt]; ?></div>
					<div class="bfi-rating-value"><?php echo  $total; ?></div>
					<div class="bfi-rating-based"><?php sprintf( JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BASED') , $summaryRatings->Count ); ?></div>
				</div>
			</div>
			<div class="bfi-col-md-4">
				<br />
				<div class="bfi-rating-title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE1') ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue1, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val1; ?>%"></div>
				</div>
				<div class="bfi-rating-title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE2') ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue2, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val2; ?>%"></div>
				</div>
				<div class="bfi-rating-title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE3') ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue3, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val3; ?>%"></div>
				</div>
			</div>
			<div class="bfi-col-md-1">
			</div>
			<div class="bfi-col-md-4">
				<br />
				<div class="bfi-rating-title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE4') ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue4, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val4; ?>%"></div>
				</div>
				<div class="bfi-rating-title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE5') ?> <span class="bfi-pull-right"><?php echo number_format((float)$summaryRatings->AValue5, 1, '.', ''); ?></span></div>
				<div class="bfi-progress">
					<div class="bfi-progress-bar" style="width: <?php echo  $val5; ?>%"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="bfi-rating-container">
			<?php $typologyId = BFCHelper::getSession('ratingsfilterstypologyid', 0 , 'com_bookingforconnector'); ?>
			<form action="<?php echo $routeMerchant; ?>" method="post" name="adminForm" id="adminForm" class="bfi-rating-filter ratingformfilter">
					<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_FILTER'); ?>
					<?php echo $genericlist; ?>
					<input type="hidden" name="filter_order" value="">
					<input type="hidden" name="filter_order_Dir" value="">
					<input type="hidden" name="searchid" value="-1">
					<input type="hidden" name="limitstart" value="0">
					<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) && ($merchant->RatingsType==0 || $merchant->RatingsType==2)) {?>
						<a href="<?php echo $routeRating; ?>" class="bfi-btn bfi-alternative bfi-pull-right"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_WRITEREVIEW') ?></a>
					<?php } ?>
			</form>
		<?php if ($ratings != null){ ?>
		<div class="bfi-merchantdetails-ratings">
			<br />
			<?php foreach($ratings as $rating){ ?>
			<?php 
			$creationDateLabel = "";
			if (isset($rating->CreationDate)) {
				
				
				$creationDate = BFCHelper::parseJsonDate($rating->CreationDate,'Y-m-d');
				$jdate  = new DateTime($creationDate,new DateTimeZone('UTC'));
				$creationDateLabel = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DATE_LABEL'), $jdate->format('d/m/Y'));
			}
			$checkInDateLabel = "";
			if (isset($rating->CheckInDate)) {
				$checkInDate = BFCHelper::parseJsonDate($rating->CheckInDate,'Y-m-d');
				$jdate  = new JDate($checkInDate);
				$checkInDateLabel = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CHECKINDATE_LABEL'), $jdate->format('F Y'));

			}
			
			$location = "";
			if ( $rating->City != ""){
				$location .= $rating->City . ", ";
			}
			$location .= $rating->Nation;
			
			$t = BFCHelper::convertTotal($rating->Total);
			
			//Reply=<risposte><risposta><![CDATA[Test risposta]]></risposta></risposte>
			
			$reply = ""; 
			$replydateLabel = ""; 
			if (!empty($rating->Reply)){					
				if (strpos($rating->Reply,'<replies>') !== false) {
					$replies = bfi_simpledom_load_string($rating->Reply);
					
					$reply = $replies->reply[0];
					$replydate = $replies->reply[0]["date"];


					if(!empty($replydate)){
						$jdatereply  = DateTime::createFromFormat('Ymd', $replydate,new DateTimeZone('UTC'));
						$replydateLabel =sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DATEREPLY_LABEL'), $jdatereply->format('d/m/Y'));
					}
				} else{
					$reply =$rating->Reply;
				}
			}
			?>
			<div class="bfi-row bfi-rating-list">
				<div class="bfi-col-md-2 ">
					<strong><?php echo  $rating->Name; ?></strong><br />
					<?php echo $location; ?><br />
					<?php 
					if(!empty($rating->TypologyId)){
						echo $list[$rating->TypologyId] ;
					}
					?><br />
					<?php if (!empty($rating->Label)) {?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php echo $checkInDateLabel; ?>
						</div>
					<?php }; ?>
					<?php if (!empty($rating->ResourceId)) {?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RESOURCE_LABEL') ?><br />
							 <?php 
								$resourceName = BFCHelper::getLanguage($rating->ResourceName, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
//								if ($itemId<>0)
//									$route = JRoute::_($uri.'&resourceId=' . $rating->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
//								else
									$route = JRoute::_($uri.'&resourceId=' . $rating->ResourceId . ':' . BFCHelper::getSlug($resourceName));
							 ?>
							<a class="" href="<?php echo $route ?>" id="nameAnchor<?php echo $rating->ResourceId?>" target="_blank"><?php echo  $resourceName ?></a>

						</div>
					<?php } ?>
						<br />
				</div>
				<div class="bfi-col-md-10 ">
					<div class="bfi-arrow-box">
						<div class="bfi-row">
							<div class="bfi-col-md-6">
								<div class="bfi-rating-value-small"><?php echo  $rating->Total; ?></div>
								<div class="bfi-rating-title-small"><?php echo $rating_text['merchants_reviews_text_value_'.$t]; ?></div>
							</div>
							<div class="bfi-col-md-6 com_bookingforconnector_rating_date_small ">
								<div class="bfi-pull-right" ><?php echo  $creationDateLabel?></div>
								<?php if (!empty($rating->Label) && !empty($rating->OrderId))  {?>
									<div class="com_bookingforconnector_rating_sign-check bfi-pull-right" ><?php sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_LABEL'), $rating->Label ); ?></div>
								<?php } ?>
							</div>
						</div>
					</div>
					
					<div class="bfi-rating-details">
					<?php if($rating->NotesData !="") {?>
						<span class=""><b>+</b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData); ?></span>
					<?php } ?>
					<?php if($rating->NotesData1 !="") {?>
						<p ><span class=""><b>-</b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData1); ?></span>
						</p>
					<?php } ?>
					</div>
					<?php if (!empty($reply)) { ?>
						<div class="bfi-rating-details bfi-arrow-box-top">
							<div class="">
							   <?php sprintf( JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_MERCHANTREPLY'), $merchantName); ?>
								<span class="com_bookingforconnector_rating_date_small bfi-pull-right"><?php echo  $replydateLabel?></span>
							</div>
							<br />
							<?php echo  $reply; ?>
						</div>
					<?php } //replies?>

				</div>
			</div>
			<?php }?>
		</div>	
		<?php } ?>
		<?php if ($ratings == null){ ?>
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_NOREVIEWS') ?>
		<?php }?>
	</div>
	
	<?php }else{?>
		<div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_NOREVIEWS')?></div>

		<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) && ($merchant->RatingsType==0 || $merchant->RatingsType==2)) {?>
			<div class="alert alert-block">
				<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NO_RESULT')?>
				<a href="<?php echo $routeRating;?>" class="bfi-btn bfi-alternative"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_WRITEREVIEW') ?></a>
			</div>
		<?php } ?>	
	<?php }?>	
</div>
<script type="text/javascript">
jQuery(function($) {
	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_PLUS')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_MINUS')?>",
		showChars: '280'
	};
	jQuery("span.expander").shorten(shortenOption);
});
</script>