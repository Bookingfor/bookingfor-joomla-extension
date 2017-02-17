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

$document     = JFactory::getDocument();
$language     = $document->getLanguage();
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
$numberOfMonth = 1;
}else{
	$numberOfMonth = 2;
}
$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();
$uri  = 'index.php?option=com_bookingforconnector&view=search';
$currModID = uniqid('search');

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0)
    $formAction = JRoute::_('index.php?Itemid='.$itemId );
else
    $formAction = JRoute::_($uri);

$checkoutspan = '+1 day';
$checkin = new JDate('now'); 
$checkout = new JDate('now'); 
$paxes = 2;
$paxages = array();
$zoneId = 0;
$merchantCategoryId = 0;
$pars = BFCHelper::getSearchParamsSession();
$bookableonly = 0;

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
	if (!empty($pars['zoneId'])) {
		$zoneId = $pars['zoneId'];
	}
	if (!empty($pars['bookableonly'])) {
		$bookableonly = $pars['bookableonly'];
	}
}
if ($checkin == $checkout){
    $checkout->modify($checkoutspan); 
}

$merchantCategories = BFCHelper::getMerchantCategoriesForRequest($language);
$listmerchantCategories = array();
$listmerchantCategories[] = JHTML::_('select.option', 0, JTEXT::_('MOD_BOOKINGFORSEARCH_SELECT') );
if(!empty($merchantCategories)){
    foreach ($merchantCategories as $mc) {
        $listmerchantCategories[] = JHTML::_('select.option', $mc->MerchantCategoryId, $mc->Name );
    }
}

$persons = array(
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') ),
    JHTML::_('select.option', '7', JText::_('7') ),
    JHTML::_('select.option', '8', JText::_('8') ),
    JHTML::_('select.option', '9', JText::_('9') ),
    JHTML::_('select.option', '10', JText::_('10') )
);


$nad = 0;
if (empty($paxages)){
    $nad = 2;
}
$nch = 0;
$nse = 0;
$countPaxes = array_count_values($paxages);

$nchs = array_values(array_filter($paxages, function($age) {
    if ($age < (int)BFCHelper::$defaultAdultsAge)
        return true;
    return false;
}));

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

$adults = array(
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') )
);

$children = array(
    JHTML::_('select.option', '0', JText::_('0') ),
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') )
);

$childrenAges = array(
    JHTML::_('select.option', '0', JText::_('0') ),
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') ),
    JHTML::_('select.option', '7', JText::_('7') ),  
    JHTML::_('select.option', '8', JText::_('8') ),
    JHTML::_('select.option', '9', JText::_('9') ),
    JHTML::_('select.option', '10', JText::_('10') ),  
    JHTML::_('select.option', '11', JText::_('11') ),  
    JHTML::_('select.option', '12', JText::_('12') ),  
    JHTML::_('select.option', '13', JText::_('13') ),  
    JHTML::_('select.option', '14', JText::_('14') ),  
    JHTML::_('select.option', '15', JText::_('15') ),  
    JHTML::_('select.option', '16', JText::_('16') ),  
    JHTML::_('select.option', '17', JText::_('17') ),  
);

$seniores = array(
    JHTML::_('select.option', '0', JText::_('0') ),
    JHTML::_('select.option', '1', JText::_('1') ),
    JHTML::_('select.option', '2', JText::_('2') ),
    JHTML::_('select.option', '3', JText::_('3') ),
    JHTML::_('select.option', '4', JText::_('4') ),
    JHTML::_('select.option', '5', JText::_('5') ),
    JHTML::_('select.option', '6', JText::_('6') )
);

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$durationId = uniqid('duration');

$show_direction = $params->get('show_direction');

$locationZones = BFCHelper::getLocationZones();
$zones = array();
$zones[] = JHTML::_('select.option', 0, JTEXT::_('MOD_BOOKINGFORSEARCH_ALL'));
if(!empty($locationZones)){
	foreach ($locationZones as $lz) {
		if(empty($zoneId) && $zoneId != 0)
			$zoneId = $lz->LocationZoneID;

		$zones[] = JHTML::_('select.option', $lz->LocationZoneID, $lz->Name);
	}
}
$duration = $checkin->diff($checkout);



$services  =  BFCHelper::getServicesForSearch($language);

$filters = BFCHelper::getFilterSearchParamsSession();

$filtersServicesValue = "";
if (isset($filters)) {
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
		$filtersServicesValue = $filters['services'];
	}
}

//get only BoxSearchable
$services = array_filter($services, function($services)  {
	return ($services->BoxSearchable && !empty($services->IconSrc));
});
if(count($services)>2 ){
	$services = array_slice($services, 0, 2);
}

?>
<div class="mod_bookingforsearch<?php echo $moduleclass_sfx ?>">
<form action="<?php echo $formAction; ?>" method="get" id="searchform<?php echo $currModID ?>">
<?php if($show_direction) :?>
	<div class="mod_bookingforsearch_inner-wrappercenter">
			<div class="divhorizontal">
				<div class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_LOCATIONZONE'); ?>:</div>
				<div class="bookingfor_field bookingfor_field_total"><?php echo JHTML::_('select.genericlist', $zones, 'locationzone', array('class' => 'inputtotal selectpicker','data-live-search' => 'true','data-width' => '99%' ), 'value', 'text', $zoneId);?></div>
			</div>
			<div class="divhorizontal">
				<div class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></div>
				<div class="bookingfor_field bookingfor_field_total"><?php echo JHTML::_('select.genericlist', $listmerchantCategories, 'merchantCategoryId' , array('onchange'=>'checkSelSearch'.$currModID.'();','class' => 'inputtotal ','data-live-search' => 'true','data-width' => '99%' ), 'value', 'text', $merchantCategoryId,'merchantCategoryId'. $currModID);?></div>
			</div>
			<div class="divhorizontal">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_FROM'); ?></span><br />
				<?php
				$checkintext = '"<div class=\'buttoncalendar checkinli'.$currModID.'\'><div class=\'dateone day\'><span>'.$checkin->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkin->format("D").'<br />'.$checkin->format("M").' '.$checkin->format("Y").'  </p></div><div class=\'dateone\'><i class=\'fa fa-calendar\'></i></div></div>"';
				?>
				<div class="dateone lastdate checking-container"><input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" /></div>
				<?php 
					echo htmlHelper::calendarimage(
						$checkin->format('m/d/Y'), 
						'checkin', 
						$checkinId, 
						'm/d/Y' /*input*/, 
						'd/m/Y' /*output*/, 
						'dd/mm/yy', 
						array('class' => 'calendar'), 
						true, 
						array(
							'numberOfMonths' => $numberOfMonth,
							'minDate' => '\'+0d\'',
							'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); insertNight<?php echo $currModID ?>()}',
							'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckinTitle'.$currModID.'(); }',
							'onChangeMonthYear' => 'function(dateText, inst) { insertCheckinTitle'.$currModID.'(); }',
							'showOn' => '"button"',
							'beforeShowDay' => 'closed'.$currModID,
							'buttonText' => $checkintext,
							'onSelect' => 'function(date) { checkDate'.$checkinId.'(jQuery, jQuery(this), date); printChangedDate'.$currModID.'(date, jQuery(this)); }',
							'firstDay' => 1
						)
					) 
				?>
				</span>
			</div>
			<div class="divhorizontal">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TO'); ?></span><br />
				<?php
				$checkouttext = '"<div class=\'buttoncalendar checkoutli'.$currModID.'\'><div class=\'dateone day\'><span>'.$checkout->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkout->format("D").'<br />'.$checkout->format("M").' '.$checkout->format("Y").'  </p></div><div class=\'dateone\'><i class=\'fa fa-calendar\'></i></div></div>"';
				?>
				<div class="dateone lastdate"><input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" /></div>
				<?php 
					echo htmlHelper::calendarimage(
						$checkout->format('m/d/Y'), 
						'checkout', 
						$checkoutId, 
						'm/d/Y' /*input*/, 
						'd/m/Y' /*output*/, 
						'dd/mm/yy', 
						array('class' => 'calendar'),
						true, 
						array(
							'numberOfMonths' => $numberOfMonth,
							'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false); }',
							'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckoutTitle'.$currModID.'(); }',
							'onSelect' => 'function(date) { printChangedDate'.$currModID.'(date, jQuery(this)); }',
							'onChangeMonthYear' => 'function(dateText, inst) { insertCheckoutTitle'.$currModID.'(); }',
							'minDate' => '\'+1d\'',
							'showOn' => '"button"',
							'beforeShowDay' => 'closed'.$currModID,
							'buttonText' => $checkouttext,
							'firstDay' => 1
						)
					) 
				?>
				</span>
			</div>
		<div class="divhorizontal">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ADULTS'); ?></span><br />
				<?php echo JHTML::_('select.genericlist', $adults, 'adults', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', $nad);?>
		</div>
		<div class="divhorizontal mod_bookingforsearch-children" id="mod_bookingforsearch-children<?php echo $currModID ?>">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDREN'); ?></span><br />
				<?php echo JHTML::_('select.genericlist', $children, 'children', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', $nch);?>
				<div class=" mod_bookingforsearch-childrenages" style="display:none;" id="mod_bookingforsearch-childrenages<?php echo $currModID ?>">
						<br />
						<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGE'); ?></span><br />
						<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages1', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[0]) ? $nchs[0] : 0);?>
						<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages2', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[1]) ? $nchs[1] : 0);?>
						<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages3', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[2]) ? $nchs[2] : 0);?>
						<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages4', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[3]) ? $nchs[3] : 0);?>
						<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages5', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[4]) ? $nchs[4] : 0);?>
				</div>
		</div>
		<div class="bfsearchfilter">
			<input type="checkbox" name="bookableonly" id="bookableonly<?php echo $currModID ?>" value="1" <?php if(!empty($bookableonly)){ echo ' checked'; }  ?> /><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_BOOKABLEONLY') ?>
		</div>
		<div id="divBtn<?php echo $currModID ?>" class="divhorizontal mod_bookingforsearch-searchbutton-wrapper">
				<a  id="aBtn2<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);" onclick="javascript: sendSearchForm<?php echo $currModID ?>()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
		</div>
	</div>
<?php else: // showdirection?> 
	<div class="mod_bookingforsearch_inner-wrapper">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 mod_bookingforsearch-param mod_bookingforsearch-zones">
				<div class="labelforaccomodation"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_LOCATIONZONE'); ?>:</div>
				<div class="selectforaccomodation"><?php echo JHTML::_('select.genericlist', $zones, 'locationzone', array('class' => 'inputtotal selectpicker','data-live-search' => 'true','data-width' => '99%' ), 'value', 'text', $zoneId);?></div>
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<div class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></div>
				<div class="bookingfor_field bookingfor_field_total"><?php echo JHTML::_('select.genericlist', $listmerchantCategories, 'merchantCategoryId' , array('onchange'=>'checkSelSearch'.$currModID.'();','class' => 'inputtotal ','data-live-search' => 'true','data-width' => '99%' ), 'value', 'text', $merchantCategoryId,'merchantCategoryId'. $currModID);?></div>
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>5">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_FROM'); ?></span><br />
				<?php
				//$checkintext = '"<div class=\'buttoncalendar checkinli\'><div class=\'dateone day\'><span>'.$checkin->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkin->format("D").'<br />'.$checkin->format("M").' '.$checkin->format("Y").'  </p></div><div class=\'dateone\'><i class=\'fa fa-calendar\'></i></div></div>"';
				$checkintext = '"<div class=\'buttoncalendar checkinli'.$currModID.'\'><div class=\'dateone day\'><span>'.$checkin->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkin->format("D").'<br />'.$checkin->format("M").' '.$checkin->format("Y").'  </p></div></div>"';
				?>
				<div class="dateone lastdate checking-container"><input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" /></div>
				<?php 
					echo htmlHelper::calendarimage(
						$checkin->format('m/d/Y'), 
						'checkin', 
						$checkinId, 
						'm/d/Y' /*input*/, 
						'd/m/Y' /*output*/, 
						'dd/mm/yy', 
						array('class' => 'calendar'), 
						true, 
						array(
							'numberOfMonths' => $numberOfMonth,
							'minDate' => '\'+0d\'',
							'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false);insertNight'.$currModID.'(); }',
							'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckinTitle'.$currModID.'(); }',
							'onChangeMonthYear' => 'function(dateText, inst) { insertCheckinTitle'.$currModID.'(); }',
							'showOn' => '"button"',
							'beforeShowDay' => 'closed'.$currModID,
							'buttonText' => $checkintext,
							'onSelect' => 'function(date) { checkDate'.$checkinId.'(jQuery, jQuery(this), date); printChangedDate'.$currModID.'(date, jQuery(this)); }',
							'firstDay' => 1
						)
					) 
				?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>5">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TO'); ?></span><br />
				<?php
				//$checkouttext = '"<div class=\'buttoncalendar checkoutli\'><div class=\'dateone day\'><span>'.$checkout->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkout->format("D").'<br />'.$checkout->format("M").' '.$checkout->format("Y").'  </p></div><div class=\'dateone\'><i class=\'fa fa-calendar\'></i></div></div>"';
				$checkouttext = '"<div class=\'buttoncalendar checkoutli'.$currModID.'\'><div class=\'dateone day\'><span>'.$checkout->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkout->format("D").'<br />'.$checkout->format("M").' '.$checkout->format("Y").'  </p></div></div>"';
				?>
				<div class="dateone lastdate"><input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" /></div>
				<?php 
					echo htmlHelper::calendarimage(
						$checkout->format('m/d/Y'), 
						'checkout', 
						$checkoutId, 
						'm/d/Y' /*input*/, 
						'd/m/Y' /*output*/, 
						'dd/mm/yy', 
						array('class' => 'calendar'),
						true, 
						array(
							'numberOfMonths' => $numberOfMonth,
							'onClose' => 'function(dateText, inst) { jQuery(this).attr("disabled", false);insertNight'.$currModID.'(); }',
							'beforeShow' => 'function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckoutTitle'.$currModID.'(); }',
							'onSelect' => 'function(date) { printChangedDate'.$currModID.'(date, jQuery(this)); }',
							'onChangeMonthYear' => 'function(dateText, inst) { insertCheckoutTitle'.$currModID.'(); }',
							'minDate' => '\'+1d\'',
							'showOn' => '"button"',
							'beforeShowDay' => 'closed'.$currModID,
							'buttonText' => $checkouttext,
							'firstDay' => 1
						)
					) 
				?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>2">
				<div class="calendarnight" id="calendarnight<?php echo $durationId ?>"><?php echo $duration->format('%a') ?></div><div class="calendarnightlabel"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT') ?></div>
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ADULTS'); ?></span><br />
				<?php echo JHTML::_('select.genericlist', $adults, 'adults', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', $nad);?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4 mod_bookingforsearch-children" id="mod_bookingforsearch-children<?php echo $currModID ?>">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDREN'); ?></span><br />
				<?php echo JHTML::_('select.genericlist', $children, 'children', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', $nch);?>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4 text-right">
<?php if(!empty($services)){
	foreach ($services as $service){
		$serviceActive ="";
		if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
			$serviceActive =" active";			
		}
  ?>
				<a href="javascript: void(0);" class="btn btn-xs btnservices <?php echo $serviceActive ?> btnservices<?php echo $currModID ?>" rel="<?php echo $service->ServiceId ?>"  aria-pressed="false"><i class="fa <?php echo $service->IconSrc ?>" aria-hidden="true"></i></a>
<?php
	  }
}
  ?>				
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> mod_bookingforsearch-childrenages" id="mod_bookingforsearch-childrenages<?php echo $currModID ?>" style="display:none;">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<span class="bookingfor_label"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGE'); ?></span><br />
				<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages1', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[0]) ? $nchs[0] : 0);?>
				<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages2', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[1]) ? $nchs[1] : 0);?>
				<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages3', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[2]) ? $nchs[2] : 0);?>
				<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages4', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[3]) ? $nchs[3] : 0);?>
				<?php echo JHTML::_('select.genericlist', $childrenAges, 'childages5', array('onchange'=>'quoteChanged'.$currModID.'();','class' => 'inputmini'), 'value', 'text', !empty($nchs[4]) ? $nchs[4] : 0);?>
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
			<div class="bfsearchfilter">
				<input type="checkbox" name="bookableonly" id="bookableonly<?php echo $currModID ?>" value="1"  <?php if(!empty($bookableonly)){ echo ' checked'; }   ?>/><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_BOOKABLEONLY') ?>
			</div>
			<br />
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 mod_bookingforsearch-searchbutton-wrapper" id="divBtn<?php echo $currModID ?>">
				<a  id="aBtn2<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);" onclick="javascript: sendSearchForm<?php echo $currModID ?>()"><i class="fa fa-search" aria-hidden="true"></i> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
			</div>
		</div>
	</div>
<?php endif; ?>

	<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
	<input type="hidden" value="1" name="newsearch" />
	<input type="hidden" value="1" name="onlystay" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="persons" id="searchformpersons<?php echo $currModID ?>" value="<?php echo $paxes ?>" />
	<input type="hidden" value="<?php echo $lang ?>" name="cultureCode" />
	<input type="hidden" id="merchantIdSearched" name="merchantId" value="" />
	<input type="hidden" id="filtersServicesSearch<?php echo $currModID ?>" name="filtersservices" value="<?php echo $filtersServicesValue ?>" />
</form>
</div>
<script type="text/javascript">
<!--

//function rePosCal(dateText, inst) {
//	var cal = inst.dpDiv;
//	var btn = jQuery(dateText).parent().find('.ui-datepicker-trigger');
//	var top  = btn.offset().top + btn.outerHeight();
//	var left = btn.offset().left;
//	setTimeout(function() {
//		cal.css({
//			"top" : top,
//			"left": left
//			});}
//		, 10);
//}

function insertNight<?php echo $currModID ?>(){
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

		locale = "en-us";
		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		jQuery('#calendarnight<?php echo $durationId ?>').html(days);
}
function insertCheckinTitle<?php echo $currModID ?>() {
	setTimeout(function() {
		jQuery("#ui-datepicker-div").addClass("checkin");
		jQuery("#ui-datepicker-div").removeClass("checkout");
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

		locale = "en-us";
		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "short" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "short" })+' luglio '+d2[2]+' (soggiorno di '+days+' notti)');
	}, 1);
}
function insertCheckoutTitle<?php echo $currModID ?>() {
	setTimeout(function() {
		jQuery("#ui-datepicker-div").addClass("checkout");
		jQuery("#ui-datepicker-div").removeClass("checkin");
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

		locale = "en-us";
		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "long" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "long" })+' '+d2[2]+' (soggiorno di '+days+' notti)');
	}, 1);
}

function closed<?php echo $currModID ?>(date) {
  var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
  var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
  date = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth()+1)).slice(-2) + "/" + date.getFullYear();
  
  var d1 = checkindate.split("/");
  var d2 = checkoutdate.split("/");
  var c = date.split("/");

  var from = new Date(d1[2], d1[1]-1, d1[0]);
  var to   = new Date(d2[2], d2[1]-1, d2[0]);
  var check = new Date(c[2], c[1]-1, c[0]);
  
  arr = [true, ''];  
  if(check.getTime() == from.getTime()) {
//  	console.log(from);
//  console.log(to);
//  console.log(check);
    arr = [true, 'date-start-selected', 'date-selected'];
  }
  if(check.getTime() == to.getTime()) {
//  	console.log(from);
//  console.log(to);
//  console.log(check);
    arr = [true, 'date-end-selected', 'date-selected'];  
  }
  if(check > from && check < to) {
    arr = [true, 'date-selected', 'date-selected'];
  }
  return arr;
}

function printChangedDate<?php echo $currModID ?>(date, elem) {
	var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

	var d1 = checkindate.split("/");
	var d2 = checkoutdate.split("/");

	var from = new Date(d1[2], d1[1]-1, d1[0]);
	var to   = new Date(d2[2], d2[1]-1, d2[0]);

	day1  = ('0' + from.getDate()).slice(-2);  
	month1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });
	year1 =  from.getFullYear();
	weekday1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { weekday: "short" });

	day2  = ('0' + to.getDate()).slice(-2); 
	month2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });              
	year2 =  to.getFullYear();
	weekday2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { weekday: "short" });

	jQuery('.checkoutli<?php echo $currModID ?>').find('.day span').html(day2);
	jQuery('.checkinli<?php echo $currModID ?>').find('.day span').html(day1);
	if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
		jQuery('.checkinli<?php echo $currModID ?>').find('.monthyear p').html(weekday1 + "<br/>" + month1+" "+year1);  
		jQuery('.checkoutli<?php echo $currModID ?>').find('.monthyear p').html(weekday2 + "<br/>" + month2+" "+year2); 
	} else {
		jQuery('.checkinli<?php echo $currModID ?>').find('.monthyear p').html(d1[1]+"/"+d1[2]);  
		jQuery('.checkoutli<?php echo $currModID ?>').find('.monthyear p').html(d2[1]+"/"+d2[2]);
	}

}
var img1 = new Image(); 
img1.src = "<?php echo JURI::root();?>media/com_bookingfor/images/loader.gif";
function checkDate<?php echo $checkinId?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate() + 1);
	$("#<?php echo $checkoutId?>").datepicker("option", "minDate", d);
}
function sendSearchForm<?php echo $currModID ?>(){
	var sel = jQuery("#merchantCategoryId<?php echo $currModID ?>")
	var selVal =  sel.val();
	if (selVal==="0")
	{
//		sel.selectpicker('setStyle', 'btn-danger', 'add');
		sel.addClass("mod_bookingforsearcherror");
	}else{
//		sel.selectpicker('setStyle', 'btn-danger', 'remove');
		sel.removeClass("mod_bookingforsearcherror");
		msg1 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG1'); ?>";
		msg2 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG2'); ?>";
		if(selVal.indexOf('id|') >= 0){
			selTipo = jQuery('select[name=merchantCategoryId] > option:first-child');
			selTipo.text('');
			selTipo.val(0);
			jQuery("#merchantIdSearched").val(selVal.replace("id|",""))
		}
		waitBlockUI(msg1, msg2,img1); 
		jQuery("#aBtn2<?php echo $currModID ?>").addClass("hide");
		jQuery("#divBtn<?php echo $currModID ?>").addClass("loading").queue(function(){
			jQuery('#searchform<?php echo $currModID ?>').submit();
			});
	}
}
function checkSelSearch<?php echo $currModID ?>() {
//	var sel = jQuery("#merchantCategoryId")
	var sel = jQuery("#merchantCategoryId<?php echo $currModID ?>")
	if (sel.val()==="0")
	{
//		sel.selectpicker('setStyle', 'btn-danger', 'add');
		sel.addClass("mod_bookingforsearcherror");
	}else{
		sel.removeClass("mod_bookingforsearcherror");
//		sel.selectpicker('setStyle', 'btn-danger', 'remove');
	}
}

function checkChildrenSearch<?php echo $currModID ?>(nch) {
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").hide();
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").hide();
	if (nch > 0) {
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").each(function(i) {
			if (i < nch) {
				var id=jQuery(this).attr('id');
				jQuery(this).show();
//				jQuery('#s2id_'+id).show();
			}
		});
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").show();
	}
	if (jQuery.prototype.masonry){
		jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
	}

}
jQuery(document).ready(function(){
//		jQuery.extend(jQuery.datepicker,{_checkOffset:function(inst,offset,isFixed){offset.top = 80; offset.left=180;return offset}});
		checkChildrenSearch<?php echo $currModID ?>(<?php echo $nch ?>);
		jQuery("#mod_bookingforsearch-children<?php echo $currModID ?> select#children").change(function() {
			checkChildrenSearch<?php echo $currModID ?>(jQuery(this).val());
		});
		jQuery(".btnservices<?php echo $currModID ?>").click(function(e) {
			e.preventDefault();
			jQuery(this).toggleClass("active");
			var active_keys = [];
			active_keys = jQuery(".btnservices.active.btnservices<?php echo $currModID ?>").map(function(index, value){
				return jQuery(value).attr('rel');
			});
			 jQuery("#filtersServicesSearch<?php echo $currModID ?>").val(jQuery.unique(active_keys.toArray()).join(","));
		});
});


function countPersone<?php echo $currModID ?>() {
	var numAdults = new Number(jQuery('#searchform<?php echo $currModID ?> #adults').val());
	var numChildren = new Number(jQuery('#mod_bookingforsearch-children<?php echo $currModID ?> select#children').val());
	var numSeniores = 0;
	jQuery('#searchformpersons<?php echo $currModID ?>').val(numAdults + numChildren + numSeniores);
}

function quoteChanged<?php echo $currModID ?>() {
	countPersone<?php echo $currModID ?>();
}

//-->
</script>
