<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . '/views/resource/resourceview.php';

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewMerchantDetails extends BFCView
{
	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false)
	{
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$item		= $this->get('Item');
		$state		= $this->get('State');
		$params 	= $state->params;
		
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('config', $config);
		$this->assignRef('sitename', $sitename);
		$this->assignRef('item', $item);
			
//		$params =  null;
		
//		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//		$model = JModelLegacy::getInstance('Resource', 'BookingForConnectorModel');
//
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$overrideFilters = array();
		
		$toExclude = array();
		$requiredOffers = array();
//		$parentId = isset($item->CondominiumId) ? $item->CondominiumId : null;
		$parentId =  null;
		$offer = null;
		if(!empty($state->params["offerId"])){
			$requiredOffers[] = $state->params["offerId"];
			$offer = $this->get('Offer');
		}
		
		if(isset($offer) && $offer->HasValidSearch) {
			$daySpan = '+'.$offer->MinDuration.' day';
			$dateparsed = BFCHelper::parseJsonDate($offer->FirstAvailableDate, 'Y-m-d');
			$overrideFilters['checkin'] = DateTime::createFromFormat('Y-m-d',$dateparsed);
			$checkInParsed = clone $overrideFilters['checkin'];
			$overrideFilters['checkout'] = $checkInParsed->modify($daySpan);
			$overrideFilters['duration'] = $offer->MinDuration;
			$params['checkin'] = $overrideFilters['checkin'];
			$params['duration'] = $offer->MinDuration;
			$params['checkout'] = $overrideFilters['checkout'];
		}
		
		$this->assignRef('params', $params);
		
		$fromSearch =  BFCHelper::getVar('s','0');
		$alternatRes = array();

		if(!empty($fromSearch)) {
			$alternatRes = BFCHelper::GetAlternateResources(0, 5, null, null, $item->MerchantId, $parentId, false, false, $toExclude, $requiredOffers, $overrideFilters);
		}

		if(!isset($alternatRes)) {
			$alternatRes = array();
		}
		$allratePlans = array();
		foreach ($alternatRes as $ratePlanStay) {
			$rs = $ratePlanStay->RatePlan;
			$rs->ResourceId = $ratePlanStay->ResourceId;
			$rs->ResName = $ratePlanStay->ResName;
			$rs->MinCapacityPaxes = $ratePlanStay->MinPaxes;
			$rs->MaxCapacityPaxes = $ratePlanStay->MaxPaxes;
			$rs->MrcCategoryName = $ratePlanStay->DefaultLangMrcCategoryName;
			$rs->Availability = $ratePlanStay->Availability;
			$rs->ImageUrl = $ratePlanStay->ImageUrl;
			$rs->ResWeight = $ratePlanStay->ResWeight;
			if ($ratePlanStay != null) {
				$rs->CalculatedPricesDetails = json_decode($rs->CalculatedPricesString);
				$rs->SelectablePrices = json_decode($rs->CalculablePricesString);
				$rs->CalculatedPackages = json_decode($rs->PackagesString);
				$rs->DiscountVariation = null;
				if(!empty($rs->Discount)){
					$rs->DiscountVariation = $rs->Discount;

				}
				$rs->SupplementVariation =null;
				if(!empty($rs->Supplement)){
					$rs->SupplementVariation = $rs->Supplement;
				}
					
				$allVar = json_decode($rs->AllVariationsString);
				$rs->Variations= [];
				$rs->SimpleDiscountIds = [];
				foreach ($allVar as $currVar) {
					$rs->Variations[] = $currVar;
					$rs->SimpleDiscountIds[] = $currVar->VariationPlanId;
					/*
					if(empty($currVar->IsExclusive)){
					}
					*/
				}
			}
			
			$allratePlans[] = $rs;
		}
		

//		echo "<pre>allratePlans";
//		echo print_r($allratePlans);
//		echo "</pre>";
				
		function cmp($a, $b)
		{
			return $a->SortOrder - $b->SortOrder;
		}

		$merchants = array();
		$merchants[] = $item->MerchantId;
		$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);

//		usort($allratePlans, "cmp");
		if(!empty($allratePlans)){
			$ResWeight = ((array_map(function ($i) { return $i->ResWeight; }, $allratePlans)));	
			$SortOrder = ((array_map(function ($i) { return $i->SortOrder; }, $allratePlans)));	

			array_multisort($ResWeight, SORT_ASC, $SortOrder, SORT_ASC , $allratePlans);
		}

	
		$this->assignRef('allstays', $allratePlans);
		$analyticsEnabled = $this->checkAnalytics("");
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		$this->assignRef('criteoConfig', $criteoConfig);
//			
//		$PriceTypes = $model->getPriceTypesFromServiceRatePlan($allstays);	//$this->get('PriceTypesFromServiceRatePlan');
//		$MerchantBookingTypes = $this->get('MerchantBookingTypesFromService');
//		$this->assignRef('Extras', $Extras);
//		$this->assignRef('PriceTypes', $PriceTypes);
//		$this->assignRef('MerchantBookingTypes', $MerchantBookingTypes);
		
		$this->setLayout('search');
		
		parent::display($tpl, $preparecontent);
	}
}
