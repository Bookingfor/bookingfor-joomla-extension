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
		JHtml::_('bootstrap.framework');

		BookingForConnectorViewResourceBase::basedisplay($tpl);
		$item		= $this->get('Item');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$params		= $this->params;

		// add stylesheet
		$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');
//		$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
		$document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/js/bootstrap-select/css/bootstrap-select.min.css');


		// load scripts
		$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
		
		$document->addScript('//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js');
		$document->addScript('//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/additional-methods.min.js');
//		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
//		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.expander.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/calendar.js');
		
		if(substr($language,0,2)!='en'){
			$document->addScript('//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_' . substr($language,0,2) . '.js');
//			$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
			$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
		}

$document->addScriptDeclaration('
var bfi_variable = {"bfi_urlCheck":"'. JRoute::_('index.php?option=com_bookingforconnector').'","bfi_cultureCode":"'.$language.'","bfi_defaultcultureCode":"'.BFCHelper::$defaultFallbackCode .'"};
');
		

		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('item', $item);
		$this->assignRef('sitename', $sitename);
		$this->assignRef('config', $config);
		
		$resource = $this->item;
		$merchant = $resource->Merchant;
		$cartType = $merchant->CartType;
		$document->addScript('components/com_bookingforconnector/assets/js/bfi.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bootstrap-select/js/bootstrap-select.js');
	if($cartType ==0 ){
		$document->addScript('components/com_bookingforconnector/assets/js/bf_cart_type_0.js');
	}else{
		$document->addScript('components/com_bookingforconnector/assets/js/bf_cart_type_1.js');
	}

		$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimePeriod.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimeSlot.js');


		
		$merchants = array();
		$merchants[] = $item->MerchantId;
		$analyticsEnabled = $this->checkAnalytics("Merchant Resources Search List");
		$criteoConfig = null;
		if(BFCHelper::getString('layout', "default") == "default") {
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
			if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
				$document->addScript('//static.criteo.net/js/ld/ld.js');
				$document->addScriptDeclaration('
				window.criteo_q = window.criteo_q || [];');
				if($item->IsCatalog) {
					$document->addScriptDeclaration('
				window.criteo_q.push( 
					{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
					{ event: "setSiteType", type: "d" }, 
					{ event: "setEmail", email: "" }, 
					{ event: "viewItem", item: "'. $criteoConfig->merchants[0] .'" }
				);');
			}
		}
			if($item->IsCatalog && $analyticsEnabled && $config->get('eecenabled', 0) == 1) {
				$obj = new stdClass;
				$obj->id = "" . $item->ResourceId . " - Resource";
				$obj->name = $item->Name;
				$obj->category = $item->MerchantCategoryName;
				$obj->brand = $item->MerchantName;
				$obj->variant = 'CATALOG';
				$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
			}
		}
		
//																	if (BFCHelper::getString('layout') == 'form') {
//																		$model = $this->getModel();
//																		$allstays = $model->getStay($language,null,true);
//															//			$allstays = $model->getStay(isset($this->params['refreshcalc']));
//																		
//																		$stay = null;
//																		
//																		if(is_array($allstays) && (!isset($this->params['pricetype']) || empty($this->params['pricetype']))) {
//																			$selStay = array_values(array_filter($allstays, function($st) {
//																				return $st->SuggestedStay->Available && $st->TotalAmount > 0;
//																			}));
//																			if(count($selStay) > 0) {
//																				$this->params['pricetype'] = $selStay[0]->RatePlanId;
//																			}
//																		}
//																		
//																		if(is_array($allstays) && isset($this->params['pricetype'])) {
//																			$selStay = array_values(array_filter($allstays, function($st) {
//																				return $this->params['pricetype'] == $st->RatePlanId;
//																			}));
//																			if(count($selStay) > 0) {
//																				$stay = $selStay[0];
//																			}
//																		}
//
//															//echo "<pre>pricetype";
//															//echo $this->params['pricetype'];
//															//echo "</pre>";
//															//
//															//			echo "<pre>selStay";
//															//						echo print_r($selStay);
//															//			echo "</pre>";
//
//															//						echo "<pre>stay";
//															//						echo print_r($stay);
//															//						echo "</pre>";
//																											
//																			/* creating totals */
//																			$total = 0;
//																			$totalDiscounted = 0;
//																			$totalWithVariation = 0;
//
//																			$newAllStays = array();
//																			if(!empty($stay)){
//																				$item->Availability = $stay->SuggestedStay->Availability;
//																				$stay->CalculatedPricesDetails = json_decode($stay->CalculatedPricesString);
//																				$stay->SelectablePrices = json_decode($stay->CalculablePricesString);
//																				$stay->CalculatedPackages = json_decode($stay->PackagesString);
//																				$stay->DiscountVariation = null;
//
//																				// only here
//																				$item->BookingType = $stay->SuggestedStay->BookingType;
//
//																				if(!empty($stay->Discount)){
//																					$stay->DiscountVariation = $stay->Discount;
//
//																				}
//																				$stay->SupplementVariation =null;
//																				if(!empty($stay->Supplement)){
//																					$stay->SupplementVariation = $stay->Supplement;
//																				}
//																					
//																				$allVar = json_decode($stay->AllVariationsString);
//																				$stay->Variations= array();
//																				$stay->SimpleDiscountIds = array();
//																				foreach ($allVar as $currVar) {
//																					if(empty($currVar->IsExclusive)){
//																						$stay->Variations[] = $currVar;
//																						$stay->SimpleDiscountIds[] = $currVar->VariationPlanId;
//																					}
//																				}
//																				if(isset($stay->BookingTypes)) {
//																					$item->MerchantBookingTypes  = $stay->BookingTypes;
//																				}
//																				if(isset($stay->SelectablePrices)) {
//																					$Extras =  $stay->SelectablePrices;
//																				}
//																				$suggestedStay = $stay->SuggestedStay;
//
//																				if(!empty($suggestedStay->TotalPrice)){
//																					$total = (float)$suggestedStay->TotalPrice;
//																				}
//																				if(!empty($suggestedStay->DiscountedPrice)){
//																					$totalDiscounted = (float)$suggestedStay->DiscountedPrice;
//																				}
//																				$totalWithVariation = $totalDiscounted;
//																				//TODO: cosa farne dei pacchetti nel calcolo singolo della risorsa?
//																				/*
//																				if(!empty($stay->CalculatedPackages)){
//																					foreach ($stay->CalculatedPackages as $pkg) {
//																						//$totalDiscounted = $totalDiscounted + $pkg->SuggestedStay->DiscountedPrice;
//																						//$total = $total + $pkg->SuggestedStay->DiscountedPrice;
//																						
//																						
//																						$totalDiscounted = $totalDiscounted + $pkg->DiscountedAmount;
//																						$total = $total + $pkg->DiscountedAmount;
//																					}
//																				}
//																				*/
//																				if(!empty($stay->Variations)){
//																					foreach ($stay->Variations as $var) {
//																						$var->TotalPackagesAmount = 0;
//																						foreach ($stay->CalculatedPackages as $pkg) {
//																							foreach($pkg->Variations as $pvar) {
//																								if($pvar->VariationPlanId == $var->VariationPlanId){
//																									$var->TotalPackagesAmount +=  $pvar->TotalAmount;
//																									break;
//																								}
//																							}
//																						}
//																					}
//																				}
//																			}
//																		
//																		
//
//																	BFCHelper::setState(array(
//																				'resourceId' => $params['resourceId'],
//																				'checkin' => $params['checkin']->format('d/m/Y'),
//																				'checkout' => $params['checkout']->format('d/m/Y'),
//																				'duration' => $params['duration'],
//																				'paxages' => $params['paxages'],
//																				'extras' => $params['extras'],
//																				'packages' => $params['packages'],
//																				'pricetype' => $params['pricetype'],
//																				'rateplanId' => $params['rateplanId'],
//																				'state' => $params['state'],
//																				'variationPlanId' => $params['variationPlanId'],
//																				'gotCalculator' => false
//																			), 'stayrequest', 'resource');
//
//																	
//																			if(!empty($item->MerchantBookingTypes)){
//																				BFCHelper::setState($item->MerchantBookingTypes, 'bookingTypes', 'resource');
//																			}
//																							
//																			BFCHelper::setState($stay, 'stay', 'resource');
//																			
//																			
//																			if(!empty($stay)){
//
//																			}
//
//																			$totalWithVariation = $totalDiscounted + 0; // force int cast
//																			$selVariationId = $params['variationPlanId'];
//																			$selVariation=null;
//																			if ($selVariationId != '') {
//																				$totalWithVariation = $total;
//																				foreach ($stay->Variations as $variation) {
//																					if($variation->VariationPlanId == $selVariationId) {
//																						$selVariation = $variation;
//																						$totalWithVariation += (float)$variation->TotalAmount + (float)$variation->TotalPackagesAmount;
//																						break;
//																					}
//																				}
//																			}		
//																	$newAllStays = array();
//
//																	foreach ($allstays as $rs) {
//																		if ($rs != null) {
//																			$rs->CalculatedPricesDetails = json_decode($rs->CalculatedPricesString);
//																			$rs->SelectablePrices = json_decode($rs->CalculablePricesString);
//																			$rs->CalculatedPackages = json_decode($rs->PackagesString);
//																			$rs->DiscountVariation = null;
//																			
//																			// only here
//																			$rs->IsBase = $rs->IsBase;
//																			$rs->BookingType =0;
//																			if(!empty($rs->SuggestedStay)){
//																				$rs->BookingType = $rs->SuggestedStay->BookingType;
//																			}
//
//																			if(!empty($rs->Discount)){
//																				$rs->DiscountVariation = $rs->Discount;
//
//																			}
//																			$rs->SupplementVariation =null;
//																			if(!empty($rs->Supplement)){
//																				$rs->SupplementVariation = $rs->Supplement;
//																			}
//																				
//																			$allVar = json_decode($rs->AllVariationsString);
//																			$rs->Variations= array();
//																			$rs->SimpleDiscountIds = array();
//																			
//																			if(!empty($allVar)){
//																				foreach ($allVar as $currVar) {
//																					$rs->Variations[] = $currVar;
//																					$rs->SimpleDiscountIds[] = $currVar->VariationPlanId;
//																					/*if(empty($currVar->IsExclusive)){
//																					}*/
//																				}
//																			}
//																		}
//																		$newAllStays[] = $rs;
//																	}
//																	
//																	
//																		$criteoConfig = BFCHelper::getCriteoConfiguration(3, $merchants);	
//																		if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
//																			$document->addScript('//static.criteo.net/js/ld/ld.js');
//																			$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
//																			window.criteo_q.push( 
//																				{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
//																				{ event: "setSiteType", type: "d" }, 
//																				{ event: "setEmail", email: "" }, 
//																				{ event: "viewBasket", item: [{ id: "' . $criteoConfig->merchants[0] . '", price: ' . $total . ', quantity: 1 }] }
//																			);');
//																		}
//																			BFCHelper::setState($totalWithVariation, 'total', 'resource');
//																			
//																			$this->assignRef('stay', $stay);
//																			$this->assignRef('total', $total);
//																			$this->assignRef('totalDiscounted', $totalDiscounted);
//																			$this->assignRef('totalWithVariation', $totalWithVariation);
//																			$this->assignRef('selVariationId', $selVariationId);
//																			$this->assignRef('selVariation', $selVariation);
//																			$this->assignRef('resstays', $newAllStays);
//															//				$this->assignRef('allstays', $allratePlans);
//																				
//																			//$Extras =  null;		//$this->get('ExtrasFromService');
//																			$PriceTypes = $model->getPriceTypesFromServiceRatePlan($allstays);	//$this->get('PriceTypesFromServiceRatePlan');
//																			$MerchantBookingTypes = $this->get('MerchantBookingTypesFromService');
//																			$this->assignRef('Extras', $Extras);
//																			$this->assignRef('PriceTypes', $PriceTypes);
//																			$this->assignRef('MerchantBookingTypes', $MerchantBookingTypes);
//
//																		} //end layout "form"
				

				$this->assignRef('analyticsEnabled', $analyticsEnabled);
				//$this->setBreadcrumb($item, 'resources');
				$this->setBreadcrumb($item, 'resources', $language);
		$this->assignRef('criteoConfig', $criteoConfig);
				
		parent::display($tpl, true);
	}
}
