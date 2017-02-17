<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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

if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	$cap = $resource->ZipCode;
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
}else{
	$addressData = $resource->AddressData;
	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
	$cap = BFCHelper::getItem($addressData, 'cap');
	$comune =  BFCHelper::getItem($addressData, 'comune');
	$provincia = BFCHelper::getItem($addressData, 'provincia');
}
if (empty($indirizzo) && empty($comune) ){

	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
		if (empty($indirizzo)){
			$indirizzo = $resource->MrcAddress;
			$cap = $resource->MrcZipCode;
			$comune = $resource->MrcCityName;
			$provincia = $resource->MrcRegionName;
		}
	}else{
		$addressData = $merchant->AddressData;
		$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
		$cap = BFCHelper::getItem($addressData, 'cap');
		$comune =  BFCHelper::getItem($addressData, 'comune');
		$provincia = BFCHelper::getItem($addressData, 'provincia');
	}
}

$merchantname = BFCHelper::getLanguage($merchant->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));

$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));
?>
<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> com_bookingforconnector_resource-t">
	<?php if ($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3 ) :?>
		<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_RETURN') ,array('class' => ' pull-right'));?>
	<?php endif; ?>
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> 
		<span class="com_bookingforconnector_resource-rating com_bookingforconnector_resource-rating<?php echo  $merchant->Rating ?>">
			<!-- <span class="com_bookingforconnector_resource-ratingText">Rating <?php echo  $merchant->Rating ?></span> -->
		</span>
	</h2>
	<div class="com_bookingforconnector_resource-address">
		<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong>
	</div>	
	<div class="clear"></div>
	<br />
	<!-- Pagina di riassunto delle recensioni  -->
	<?php
$listfiltered ="";
if(!empty( $this->params['filters'])){
	$filters = $this->params['filters'];
	$listfiltered =  $filters['typologyid'];
}

// preparo la lista per i filtri..
$list = BFCHelper::parseArrayList(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_CONSTANTS_RATING_TYPOLOGIESLIST'));
$genericlist = JHTML::_('select.genericlist', $list, 'filters[typologyid]',array('onchange' => 'this.form.submit();') , 'value', 'text', $listfiltered);
		
		$summaryRatings = $this->getModel()->getRatingAverageFromService($merchant->MerchantId,$resource->ResourceId);
	 ?>
	<?php if (isset($summaryRatings)): ?>
	<?php 
		$val1 = round($summaryRatings->AValue1 * 10);
		$val2 = round($summaryRatings->AValue2 * 10);
		$val3 = round($summaryRatings->AValue3 * 10);
		$val4 = round($summaryRatings->AValue4 * 10);
		$val5 = round($summaryRatings->AValue5 * 10);
		$total = number_format((float)$summaryRatings->Average, 1, '.', '');
		//$totalInt = intval($total, 10); 
		$totalInt = BFCHelper::convertTotal($total);

	 ?>
	<div class="resourcetabcontainer">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 text-center">
				<div class="com_bookingforconnector_rating_valuation">
					<div class="com_bookingforconnector_rating_title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_'.$totalInt) ?></div>
					<div class="com_bookingforconnector_rating_value"><?php echo  $total; ?></div>
					<div class="com_bookingforconnector_rating_based"><?php echo JText::sprintf('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BASED', $summaryRatings->Count); ?></div>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
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
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
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
				<?php if (($merchant->RatingsContext === 2 || $merchant->RatingsContext === 3)   ) :?>
					<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=rating&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BUTTON_COMMENT') ,array('class' => 'btn btn-warning pull-right'));?>
				<?php endif; ?>
			</fieldset>
			</form>

		</div>
		<?php if ($this->items != null): ?>
		<div class="com_bookingforconnector_merchantdetails-ratings">
			<br />
			<?php foreach($this->items as $rating): ?>
			<?php 
			$creationDateLabel = "";
			if (isset($rating->CreationDate)) {
				
				
				$creationDate = BFCHelper::parseJsonDate($rating->CreationDate,'Y-m-d');
				//$creationDate =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($rating->CreationDate,$formatDate));
				$jdate  = new JDate($creationDate); // 3:20 PM, December 1st, 2012
				$creationDateLabel = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DATE_LABEL'), $jdate->toFormat('%d %B %Y'));
			}
			$checkInDateLabel = "";
			if (isset($rating->CheckInDate)) {
				$checkInDate = BFCHelper::parseJsonDate($rating->CheckInDate,'Y-m-d');
				//$creationDate =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($rating->CreationDate,$formatDate));
				$jdate  = new JDate($checkInDate); // 3:20 PM, December 1st, 2012
				$checkInDateLabel = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_CHECKINDATE_LABEL'), $jdate->toFormat('%B %Y'));
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
				$replies = simpledom_load_string($rating->Reply);
				
				$reply = $replies->reply[0];
				$replydate = $replies->reply[0]["date"];
				$jdatereply  = new JDate(strtotime($replydate),2); // 3:20 PM, December 1st, 2012
				$replydateLabel =sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_DATEREPLY_LABEL'), $jdatereply->toFormat('%d %B %Y', true));
							
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
			<?php if ($this->pagination->get('pages.total') > 1) : ?>
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php endif; ?>
		</div>	
		<?php endif?>	
	</div>

		<?php else:?>
		<div class="alert alert-block">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NO_RESULT')?>
			<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=rating&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,-1), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_BUTTON_COMMENT') ,array('class' => ' btn btn-info'));?>
		</div>
		<?php endif?>	
</div>
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
