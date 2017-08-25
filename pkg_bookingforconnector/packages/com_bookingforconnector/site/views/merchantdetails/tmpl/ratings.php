<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$sitename = $this->sitename;
$language = $this->language;
$merchant = $this->item;
$merchantname = BFCHelper::getLanguage($merchant->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

//$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_REVIEWS_TITLE'),$merchant->Name,$sitename));

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());
$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	$itemIdMerchant = intval($db->loadResult());
}
$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$uriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant = JRoute::_($uriMerchant);
$routeRating = JRoute::_($uriMerchant.'&layout=rating');				

$listOrder="";
$listDirn="";
$searchid="-1";


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

$rating = $merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
}


?>
<div class="bfi-content">
	<div class="bfi-title-name"><?php echo  $merchant->Name?> 
		<span class="bfi-item-rating">
		  <?php for($i = 0; $i < $rating; $i++) { ?>
		  <i class="fa fa-star"></i>
		  <?php } ?>
		</span>
	</div>

	<div class="clear"></div>
	<?php
	$list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
	$listfiltered = isset($_SESSION['ratings']['filters']['typologyid']) ? $_SESSION['ratings']['filters']['typologyid'] : 0;
	$genericlist = JHTML::_('select.genericlist', $list, 'filters[typologyid]',array('onchange' => 'this.form.submit();') , 'value', 'text', $listfiltered);

	$summaryRatings = BFCHelper::getRatingByMerchantId($merchant->MerchantId);

////$searchid =  $this->params['searchid'];
//$filters = $this->params['filters'];
////$listOrder	= $this->escape($this->state->get('list.ordering'));
////$listDirn	= $this->escape($this->state->get('list.direction'));
//
//// preparo la lista per i filtri..
//$list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
//$listfiltered =  $filters['typologyid'];
////foreach( $list as $key=>$value) {
////	$options1[] = JHTML::_( 'select.option', $key, $value );
////}
////$radiolist = JHTML::_('select.radiolist', $options1, 'filters[typologyid]',array('onchange' => 'this.form.submit();', 'class'=> 'rbfilter') , 'value', 'text', $listfiltered);
//$genericlist = JHTML::_('select.genericlist', $list, 'filters[typologyid]',array('onchange' => 'this.form.submit();') , 'value', 'text', $listfiltered);
//
////echo ("<pre>");	
////echo (print_r($list));	
////echo ("</pre>");	
//
	$ratings = $this->items		;
	$summaryRatings = $this->getModel()->getMerchantRatingAverageFromService();
	if(isset($summaryRatings)) {
		$val1 = round($summaryRatings->AValue1 * 10);
		$val2 = round($summaryRatings->AValue2 * 10);
		$val3 = round($summaryRatings->AValue3 * 10);
		$val4 = round($summaryRatings->AValue4 * 10);
		$val5 = round($summaryRatings->AValue5 * 10);
		$total = number_format((float)$summaryRatings->Average, 1, '.', '');

		$totalInt = BFCHelper::convertTotal($total);
	?>
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
			<?php $typologyId = isset($_SESSION['ratings']['filters']['typologyid']) ? $_SESSION['ratings']['filters']['typologyid'] : 0; ?>
			<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="bfi-rating-filter ratingformfilter">
					<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_FILTER'); ?>
					<?php echo $genericlist; ?>
					<input type="hidden" name="filter_order" value="">
					<input type="hidden" name="filter_order_Dir" value="">
					<input type="hidden" name="searchid" value="-1">
					<input type="hidden" name="limitstart" value="0">
					<a href="<?php echo $routeRating; ?>" class="bfi-btn bfi-alternative bfi-pull-right"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_WRITEREVIEW') ?></a>
			</form>
		<?php if ($ratings != null){ ?>
		<div class="bfi-merchantdetails-ratings">
			<br />
			<?php foreach($ratings as $rating){ ?>
			<?php 
			$creationDateLabel = "";
			if (isset($rating->CreationDate)) {
				
				
				$creationDate = BFCHelper::parseJsonDate($rating->CreationDate,'Y-m-d');
				$jdate  = new DateTime($creationDate);
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
//					$jdatereply  = new JDate(strtotime($replydate),2); // 3:20 PM, December 1st, 2012
					$jdatereply  = DateTime::createFromFormat('Ymd', $replydate);

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
					<?php } ?>
					<?php if (!empty($rating->ResourceId)) {?>
						<br />
						<div class="bfi-rating-lineheight">
							<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RESOURCE_LABEL') ?><br />
							 <?php 
								$resourceName = BFCHelper::getLanguage($rating->ResourceName, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
								if ($itemId<>0)
									$route = JRoute::_($uri.'&resourceId=' . $rating->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
								else
									$route = JRoute::_($uri.'&resourceId=' . $rating->ResourceId . ':' . BFCHelper::getSlug($resourceName));
							 ?>
							<a class="" href="<?php echo $route ?>" id="nameAnchor<?php echo $rating->ResourceId?>"><?php echo  $resourceName ?></a>

						</div>
					<?php } ?>
						<br />
				</div>
				<div class="bfi-col-md-10">
					<div class="bfi-arrow-box">
						<div class="bfi-row">
							<div class="bfi-col-md-6">
								<div class="bfi-rating-value-small"><?php echo  $rating->Total; ?></div>
								<div class="bfi-rating-title-small"><?php echo $rating_text['merchants_reviews_text_value_'.$t]; ?></div>
							</div>
							<div class="bfi-col-md-6 com_bookingforconnector_rating_date_small ">
								<div class="bfi-pull-right" ><?php echo  $creationDateLabel?></div>
								<?php if (!empty($rating->Label) && !empty($rating->ResourceId)) {?>
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
						<p><span class=""><b>-</b></span>
						<span class="expander"><?php echo  stripslashes($rating->NotesData1); ?></span></p>
					<?php } ?>
					</div>
					<?php if (!empty($reply)) { ?>
						<div class="bfi-rating-details bfi-arrow-box-top">
							<div class="">
							   <?php echo sprintf( JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_MERCHANTREPLY'), $merchantname); ?>
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
					<?php if ($this->pagination->get('pages.total') > 1) { ?>
						<div class="pagination">
							<?php echo $this->pagination->getPagesLinks(); ?>
						</div>
					<?php } ?>

		<?php }else{ ?>
			<?php if ($merchant->RatingsContext !== 0 && $merchant->RatingsContext !== 2 ) {?>
				<div class="alert alert-block">
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NO_RESULT')?>
					<?php if (($this->item->RatingsContext === 1 || $this->item->RatingsContext === 3) && ($this->item->RatingsType==0 || $this->item->RatingsType==2)) :?>
						<a href="<?php echo $routeRating;?>" class="bfi-btn bfi-alternative"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_WRITEREVIEW') ?></a>
					<?php endif?>	
				</div>
			<?php }?>	
		<?php } ?>	
	<div class="bfi-clearboth"></div>
	<?php  include(JPATH_COMPONENT.'/views/shared/merchant_small_details.php');  ?>

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