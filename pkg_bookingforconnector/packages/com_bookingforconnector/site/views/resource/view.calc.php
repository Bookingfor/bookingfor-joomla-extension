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
class BookingForConnectorViewResource extends BookingForConnectorViewResourceBase
{
	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false)
	{
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
				
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('config', $config);
		$this->assignRef('sitename', $sitename);
			
			$params =  null;
			$Extras =  null;		//$this->get('ExtrasFromService');
			$PriceTypes =  null;		//$this->get('PriceTypesFromService');
			$additionalJsScripts = "";
			$model = $this->getModel();
		BookingForConnectorViewResourceBase::basedisplay($tpl);
		$item		= $this->item;
		$params		= $this->params;
		
//		$item->Availability = 0;
//		
//		$allstays = $model->getStay($language,isset($this->params['refreshcalc']));
//		//$model->getResourceRatePlanFacilieties($item, $stay);
//		
//		$stay = null;
//
//				
//		if(is_array($allstays) && (!isset($this->params['pricetype']) || empty($this->params['pricetype']))) {
//			$selStay = array_values(array_filter($allstays, function($st) {
//				$resAvailable = 0;
//				if(!empty( $st->SuggestedStay)){
//					$resAvailable = $st->SuggestedStay->Available;
//				}
//				return $resAvailable && $st->TotalAmount > 0;
//			}));
//			if(count($selStay) > 0) {
//				$this->params['pricetype'] = $selStay[0]->RatePlanId;
//			}
//		}
//		
//		if(is_array($allstays) && isset($this->params['pricetype']) && !empty($this->params['pricetype'])) {
//			$prt = $this->params['pricetype'];
//			$selStay = array_values(array_filter($allstays, function($st) use($prt) {
//				return $prt == $st->RatePlanId;
//			}));			
//			if(count($selStay) > 0) {
//				$stay = $selStay[0];
//			}
//			if(!empty($stay)){
//				$item->Availability = $stay->SuggestedStay->Availability;
//			}
//		}
//
//		if(isset($stay->BookingTypes)) {
//			$item->MerchantBookingTypes  = $stay->BookingTypes;
//		}
//		if(isset($stay->SelectablePrices)) {
//			$Extras =  $stay->SelectablePrices;
//		}
//		// Check for errors.
//		if (count($errors = $this->get('Errors'))) {
//			BFCHelper::raiseWarning(500, implode("\n", $errors));
//			return false;
//		}
//		
//		// saves state for accessing from other plugins and components (RSForms specifically) from now on
//		BFCHelper::setState(array(
//					'resourceId' => $params['resourceId'],
//					'checkin' => $params['checkin']->format('d/m/Y'),
//					'checkout' => $params['checkout']->format('d/m/Y'),
//					'duration' => $params['duration'],
//					'paxages' => $params['paxages'],
//					'extras' => $params['extras'],
//					'packages' => $params['packages'],
//					'pricetype' => $params['pricetype'],
//					'rateplanId' => $params['rateplanId'],
//					'state' => $params['state'],
//					'variationPlanId' => $params['variationPlanId'],
//					'gotCalculator' => false
//				), 'stayrequest', 'resource');
//
		
//		if(!empty($item->MerchantBookingTypes)){
//			BFCHelper::setState($item->MerchantBookingTypes, 'bookingTypes', 'resource');
//		}
		
//		BFCHelper::setState($stay, 'stay', 'resource');
//		BFCHelper::setState($item->Merchant, 'merchant', 'resource');
		
		/* creating totals */
//		$total = 0;
//		$totalDiscounted = 0;
//		$totalWithVariation = 0;
//		if(!empty($stay->TotalPrice)){
//			$total = $stay->TotalPrice;
//		}
//		if(!empty($stay->DiscountedPrice)){
//			$totalDiscounted = $stay->DiscountedPrice;
//		}
//		$totalWithVariation = $totalDiscounted;
//		
//		if(!empty($stay)){
//			//TODO: cosa farne dei pacchetti nel calcolo singolo della risorsa?
//			/*
//			if(!empty($stay->CalculatedPackages)){
//				foreach ($stay->CalculatedPackages as $pkg) {
//					//$totalDiscounted = $totalDiscounted + $pkg->SuggestedStay->DiscountedPrice;
//					//$total = $total + $pkg->SuggestedStay->DiscountedPrice;
//					
//					
//					$totalDiscounted = $totalDiscounted + $pkg->DiscountedAmount;
//					$total = $total + $pkg->DiscountedAmount;
//				}
//			}
//			*/
//			if(!empty($stay->Variations)){
//				foreach ($stay->Variations as $var) {
//					$var->TotalPackagesAmount = 0;
//					/*
//					foreach ($stay->CalculatedPackages as $pkg) {
//						foreach($pkg->Variations as $pvar) {
//							if($pvar->VariationPlanId == $var->VariationPlanId){
//								$var->TotalPackagesAmount +=  $pvar->TotalAmount;
//								break;
//							}
//						}
//					}
//					*/
//				}
//			}
//		}
//
//		$totalWithVariation = $totalDiscounted + 0; // force int cast
//		$selVariationId = $params['variationPlanId'];
//		if ($selVariationId != '') {
//			foreach ($stay->Variations as $variation) {
//				if($variation->VariationPlanId == $selVariationId) {
//					$totalWithVariation += $variation->TotalAmount + $variation->TotalPackagesAmount;
//					break;
//				}
//			}
//		}
//		$newAllStays = array();
//		foreach ($allstays as $rs) {
//			//$rs = $ratePlanStay;
//			$rs->MrcCategoryName = $item->MerchantCategoryName;
//			if ($rs != null) {
//				$rs->CalculatedPricesDetails = json_decode($rs->CalculatedPricesString);
//				$rs->SelectablePrices = json_decode($rs->CalculablePricesString);
//				$rs->CalculatedPackages = json_decode($rs->PackagesString);
//				$rs->DiscountVariation = null;
//				if(!empty($rs->Discount)){
//					$rs->DiscountVariation = $rs->Discount;
//
//				}
//				$rs->BookingType =0;
//				if(!empty($rs->SuggestedStay)){
//					$rs->BookingType = $rs->SuggestedStay->BookingType;
//				}
//
//				$rs->SupplementVariation =null;
//				if(!empty($rs->Supplement)){
//					$rs->SupplementVariation = $rs->Supplement;
//				}
//					
//				$allVar = json_decode($rs->AllVariationsString);
//				$rs->Variations= [];
//				$rs->SimpleDiscountIds = [];
//				
//				if(!empty($allVar)){
//					foreach ($allVar as $currVar) {
//						$rs->Variations[] = $currVar;
//						$rs->SimpleDiscountIds[] = $currVar->VariationPlanId;
//						/*if(empty($currVar->IsExclusive)){
//						}*/
//					}
//				}
//			}
//			$newAllStays[] = $rs;
//		}
//		
//		
//		$toExclude = array();
//		$toExclude[] = $item->ResourceId;
//		$parentId = isset($item->CondominiumId) ? $item->CondominiumId : null;
//		$alternatRes = $model->getSearchResults(0, 5, null, null, $item->Merchant->MerchantId, $parentId, false, false, $toExclude);
//		if(!isset($alternatRes)) {
//			$alternatRes = array();
//		}
//		$allratePlans = array();
//		foreach ($alternatRes as $ratePlanStay) {
//			$rs = $ratePlanStay->RatePlan;
//			$rs->ResourceId = $ratePlanStay->ResourceId;
//			$rs->ResName = $ratePlanStay->ResName;
//			$rs->MrcName = $ratePlanStay->MrcName;
//			$rs->MinCapacityPaxes = $ratePlanStay->MinPaxes;
//			$rs->MaxCapacityPaxes = $ratePlanStay->MaxPaxes;
//			$rs->Availability = $ratePlanStay->Availability;
//			$rs->MrcCategoryName = $item->MerchantCategoryName;
//			$rs->ImageUrl = $ratePlanStay->ImageUrl;
//			if ($ratePlanStay != null) {
//				$rs->CalculatedPricesDetails = json_decode($rs->CalculatedPricesString);
//				$rs->SelectablePrices = json_decode($rs->CalculablePricesString);
//				$rs->CalculatedPackages = json_decode($rs->PackagesString);
//				$rs->DiscountVariation = null;
//				$rs->BookingType =0;
//				if(!empty($rs->SuggestedStay)){
//					$rs->BookingType = $rs->SuggestedStay->BookingType;
//				}
//				if(!empty($rs->Discount)){
//					$rs->DiscountVariation = $rs->Discount;
//
//				}
//				$rs->SupplementVariation =null;
//				if(!empty($rs->Supplement)){
//					$rs->SupplementVariation = $rs->Supplement;
//				}
//					
//				$allVar = json_decode($rs->AllVariationsString);
//				$rs->Variations= [];
//				$rs->SimpleDiscountIds = [];
//				foreach ($allVar as $currVar) {
//					$rs->Variations[] = $currVar;
//					$rs->SimpleDiscountIds[] = $currVar->VariationPlanId;
//					/*
//					if(empty($currVar->IsExclusive)){
//					}
//					*/
//				}
//			}
//			
//			$allratePlans[] = $rs;
//		}
//		
//		
//		function cmp($a, $b)
//		{
//			return $a->SortOrder - $b->SortOrder;
//		}
//
//		usort($allratePlans, "cmp");
		
		$merchants = array();
		$merchants[] = $item->MerchantId;
		$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
		
//		BFCHelper::setState($totalWithVariation, 'total', 'resource');
		
//		$this->assignRef('stay', $stay);
//		$this->assignRef('resstays', $newAllStays);
//		$this->assignRef('allstays', $allratePlans);
//		$this->assignRef('total', $total);
//		$this->assignRef('totalDiscounted', $totalDiscounted);
//		$this->assignRef('totalWithVariation', $totalWithVariation);
		$analyticsEnabled = $this->checkAnalytics("") && $config->get('eecenabled', 0) == 1;
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		$this->assignRef('criteoConfig', $criteoConfig);
		$this->assignRef('model', $model);
			
			//$Extras =  null;		//$this->get('ExtrasFromService');
//			$PriceTypes = $model->getPriceTypesFromServiceRatePlan($allstays);	//$this->get('PriceTypesFromServiceRatePlan');
//			$MerchantBookingTypes = $this->get('MerchantBookingTypesFromService');
//		$this->assignRef('Extras', $Extras);
//		$this->assignRef('PriceTypes', $PriceTypes);
//		$this->assignRef('MerchantBookingTypes', $MerchantBookingTypes);
		
		$this->setLayout('calc');
		
		parent::display($tpl, $preparecontent);
	}
}
