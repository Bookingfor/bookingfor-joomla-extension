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
defined('_JEXEC') or die('Restricted access');
$language = $this->language;
$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());

$resource = $this->item;
$merchant = $resource->Merchant;

$merchantname = BFCHelper::getLanguage($merchant->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$listOrder="";
$listDirn="";
$searchid="-1";

?>
	<!-- Pagina di riassunto delle recensioni  -->
	<?php
    // preparo la lista per i filtri..
    $list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
    $listfiltered = array();
    if(isset($this->params['filters'])) {
      $filters = $this->params['filters'];
      $listfiltered =  $filters['typologyid'];
    }
    $genericlist = JHTML::_('select.genericlist', $list, 'filters[typologyid]',array('onchange' => 'this.form.submit();') , 'value', 'text', $listfiltered);
		
	 $summaryRatings = $this->getModel()->getRatingAverageFromService($merchant->MerchantId,$resource->ResourceId);
	 ?>
	<?php if (isset($summaryRatings)): ?>
	<?php
	   $ratings =  $this->getModel()->getRatingsFromService(0, 20, $resource->ResourceId);
		$val1 = round($summaryRatings->AValue1 * 10);
		$val2 = round($summaryRatings->AValue2 * 10);
		$val3 = round($summaryRatings->AValue3 * 10);
		$val4 = round($summaryRatings->AValue4 * 10);
		$val5 = round($summaryRatings->AValue5 * 10);
		$total = number_format((float)$summaryRatings->Average, 1, '.', '');
		$totalInt = BFCHelper::convertTotal($total);
	?>
<div class="com_bookingforconnector_rating-ratings-wrapper">
<div class="com_bookingforconnector_resource ">
	<div class="resourcetabcontainer">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector_rating-ratings-summary-item">
				<div class="com_bookingforconnector_rating_valuation">
					<div class="com_bookingforconnector_rating_title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_'.$totalInt) ?></div>
					<div class="com_bookingforconnector_rating_value"><?php echo  $total; ?></div>
					<div class="com_bookingforconnector_rating_based"><?php echo JText::sprintf('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BASED', $summaryRatings->Count); ?></div>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector_rating-ratings-summary-item">
				<br />
				<div class="com_bookingforconnector_rating_title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE1') ?> <span class="pull-right"><?php echo number_format((float)$summaryRatings->AValue1, 1, '.', ''); ?></span></div>
				<div class="progress progress-overrides">
					<div class="bar progress-bar" style="width: <?php echo  $val1; ?>%"></div>
				</div>
				<div class="com_bookingforconnector_rating_title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE2') ?> <span class="pull-right"><?php echo number_format((float)$summaryRatings->AValue2, 1, '.', ''); ?></span></div>
				<div class="progress progress-overrides">
					<div class="bar progress-bar" style="width: <?php echo  $val2; ?>%"></div>
				</div>
				<div class="com_bookingforconnector_rating_title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE3') ?> <span class="pull-right"><?php echo number_format((float)$summaryRatings->AValue3, 1, '.', ''); ?></span></div>
				<div class="progress progress-overrides">
					<div class="bar progress-bar" style="width: <?php echo  $val3; ?>%"></div>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector_rating-ratings-summary-item">
				<br />
				<div class="com_bookingforconnector_rating_title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE4') ?> <span class="pull-right"><?php echo number_format((float)$summaryRatings->AValue4, 1, '.', ''); ?></span></div>
				<div class="progress progress-overrides">
					<div class="bar progress-bar" style="width: <?php echo  $val4; ?>%"></div>
				</div>
				<div class="com_bookingforconnector_rating_title_desc"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUE5') ?> <span class="pull-right"><?php echo number_format((float)$summaryRatings->AValue5, 1, '.', ''); ?></span></div>
				<div class="progress progress-overrides">
					<div class="bar progress-bar" style="width: <?php echo  $val5; ?>%"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="resourcetabcontainer">
		<div class="filter alert alert-rating">
			<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline ratingformfilter">
			<fieldset class="filters">
				<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_FILTER'); ?> <?php echo $genericlist; ?>
				<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
				<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
				<input type="hidden" name="limitstart" value="0" />
				<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) && ($merchant->RatingsType==0 || $merchant->RatingsType==2)) :?>
					<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=rating&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BUTTON_COMMENT') ,array('class' => 'btn btn-warning pull-right'));?>
				<?php endif; ?>
			</fieldset>
			</form>

		</div>
		<?php if ($ratings != null): ?>
		<div class="com_bookingforconnector_merchantdetails-ratings">
			<br />
			<?php foreach($ratings as $rating): ?>
			<?php 
			$creationDateLabel = "";
			if (isset($rating->CreationDate)) {
				
				
				$creationDate = BFCHelper::parseJsonDate($rating->CreationDate,'Y-m-d');
				//$creationDate =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($rating->CreationDate,$formatDate));
				$jdate  = new JDate($creationDate); // 3:20 PM, December 1st, 2012
				$creationDateLabel = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DATE_LABEL'), $jdate->format('d/m/Y'));
			}
			$checkInDateLabel = "";
			if (isset($rating->CheckInDate)) {
				$checkInDate = BFCHelper::parseJsonDate($rating->CheckInDate,'Y-m-d');
				//$creationDate =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($rating->CreationDate,$formatDate));
				$jdate  = new JDate($checkInDate); // 3:20 PM, December 1st, 2012
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
					$replies = simpledom_load_string($rating->Reply);
					
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
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_rating_list">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 ">
					<strong><?php echo  $rating->Name; ?></strong><br />
					<?php echo $location; ?><br />
					<?php echo $list[$rating->TypologyId]; ?><br />
					<?php if (!empty($rating->Label)) :?>
						<br />
						<div class="com_bookingforconnector_rating_lineheight">
							<?php echo $checkInDateLabel; ?>
						</div>
					<?php endif; ?>
					<?php if (!empty($rating->ResourceId)) :?>
						<br />
						<div class="com_bookingforconnector_rating_lineheight">
							<?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RESOURCE_LABEL') ?><br />
							 <?php 
								$resourceName = BFCHelper::getLanguage($rating->ResourceName, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
								//$resourceName= "Appartamento in fondo al mare";

								if ($itemId<>0)
									$route = JRoute::_($uri.'&resourceId=' . $rating->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
								else
									$route = JRoute::_($uri.'&resourceId=' . $rating->ResourceId . ':' . BFCHelper::getSlug($resourceName));
							 ?>
							<a class="" href="<?php echo $route ?>" id="nameAnchor<?php echo $resource->ResourceId?>"><?php echo  $resourceName ?></a>

						</div>
					<?php endif; ?>
						<br />
				</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10 ">
					<div class=" arrow_box ">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
								<div class="com_bookingforconnector_rating_value_small"><?php echo  $rating->Total; ?></div>
								<div class="com_bookingforconnector_rating_title_small"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_'.$t) ?></div>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 com_bookingforconnector_rating_date_small ">
								<div class="pull-right" ><?php echo  $creationDateLabel?></div>
								<?php if (!empty($rating->Label) && !empty($rating->ResourceId)) :?>
									<div class="com_bookingforconnector_rating_sign-check pull-right" ><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_LABEL'), $rating->Label) ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					
					<div class=" rating_details ">
					<?php if($rating->NotesData !="") :?>
						<p > <span class="label label-info"><b>+</b></span>
						<span class="expander"><?php echo  $rating->NotesData; ?></span>
						</p>
						<br />
					<?php endif; ?>
					<?php if($rating->NotesData1 !="") :?>
						<p ><span class="label label-warning"><b>-</b></span>
						<span class="expander"><?php echo  $rating->NotesData1; ?></span>
						</p>
					<?php endif; ?>
					</div>
					<?php if (!empty($reply)) : ?>
						<div class=" rating_details arrow_box_top">
							<div class="">
								<?php echo sprintf(JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_MERCHANTREPLY'), $merchantname) ?>
								<span class="com_bookingforconnector_rating_date_small pull-right"><?php echo  $replydateLabel?></span>
							</div>
							<br />
							<?php echo  $reply; ?>
						</div>
					<?php endif; //replies?>

				</div>
			</div>
			<?php endforeach?>
			<?php if (!empty($this->pagination) && $this->pagination->get('pages.total') > 1) : ?>
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php endif; ?>
		</div>	
		<?php endif?>	
	</div>
</div>
</div>

		<?php else:?>
			<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) && ($merchant->RatingsType==0 || $merchant->RatingsType==2)) :?>
				<div class="com_bookingforconnector_rating-ratings-wrapper">
					<div class="com_bookingforconnector_resource ">
						<div class="alert alert-block">
							<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NO_RESULT')?>
							<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=rating&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BUTTON_COMMENT') ,array('class' => ' btn btn-info'));?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif?>	
<script type="text/javascript">
jQuery(function($) {
	$('.moduletable-insearch').show();
	$('span.expander').expander({
		slicePoint: 280,
		expandText:'<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_PLUS')?>',
		userCollapseText: '<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_MINUS')?>'
	});
});
</script>
