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

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewSearch extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;
	protected $typologies = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{

		JHtml::_('bootstrap.framework');

				// Initialise variables
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$maxItemsView = 3;
		$this->assignRef('maxItemsView', $maxItemsView);
		$state		= $this->get('State');
		$params = $state->params;
		if(!isset($params) || empty($params['checkin']) ){
			header ("Location: ". JURI::root()); 
			$app->close();
		}
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		$typologies = $this->getModel()->getMasterTypologies(true,$language);
	
		// add stylesheet
		
		JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
		$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');

		// load scripts
		$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.ui.touch-punch.min.js');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		$merchants = array();
		if(!empty($items)) {
			$merchants = array_unique(array_map(function($a) { return $a->MerchantId; }, $items));
		}
		$criteoConfig = BFCHelper::getCriteoConfiguration(1, $merchants);
		if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
			$document->addScript('//static.criteo.net/js/ld/ld.js');
			$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
			window.criteo_q.push( 
				{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
				{ event: "setSiteType", type: "d" }, 
				{ event: "setEmail", email: "" }, 
				{ event: "viewSearch", checkin_date: "' . $params["checkin"]->format('Y-m-d') . '", checkout_date: "' . $params["checkout"]->format('Y-m-d') . '"},
				{ event: "viewList", item: ' . json_encode($criteoConfig->merchants) .' }
			);');
		}
		
		$totalItems = array();
		$merchantResults = $params['merchantResults'];
		$condominiumsResults = $params['condominiumsResults'];
		$listName = "";
		$sendData = true;
		
		
		if(!empty($items)) {
			if($merchantResults) {
				$resIndex = 0;
				$listName = "Resources Group List";
				foreach($items as $mrckey => $mrcValue) {
					$obj = new stdClass();
					$obj->Id = $mrcValue->MerchantId . " - Merchant";
					$obj->MerchantId = $mrcValue->MerchantId;
					$obj->Name = $mrcValue->Name;
					$obj->MrcCategoryName = $mrcValue->MrcCategoryName;
					$obj->MrcName = $mrcValue->Name;
					$obj->Position = $mrckey;
					$totalItems[] = $obj;
					foreach($mrcValue->Resources as $resKey => $resValue) {
						$objRes = new stdClass();
						$objRes->Id = $resValue->ResourceId . " - Resource";
						$objRes->MerchantId = $mrcValue->MerchantId;
						$objRes->Name = $resValue->ResName;
						$objRes->MrcName = $mrcValue->Name;
						$objRes->MrcCategoryName = $mrcValue->MrcCategoryName;
						$objRes->Position = $resIndex;
						if($resKey >= $maxItemsView) {
							$objRes->ExcludeInitial = true;
						}
						$totalItems[] = $objRes;
						$resIndex++;
					}
				}
			} else if ($condominiumsResults) {
				$sendData = false;
				$resIndex = 0;
				$listName = "Resources Group List";
				foreach($items as $mrckey => $mrcValue) {
					$obj = new stdClass();
					$obj->Id = $mrcValue->CondominiumId . " - Resource Group";
					$obj->MerchantId = $mrcValue->MerchantId;
					$obj->Name = $mrcValue->Name;
					$obj->MrcCategoryName = $mrcValue->MrcCategoryName;
					$obj->MrcName = $mrcValue->MerchantName;
					$obj->Position = $mrckey;
					$totalItems[] = $obj;
					foreach($mrcValue->Resources as $resKey => $resValue) {
						$objRes = new stdClass();
						$objRes->Id = $resValue->ResourceId . " - Resource";
						$objRes->GroupId = $mrcValue->CondominiumId;
						$objRes->MerchantId = $mrcValue->MerchantId;
						$objRes->Name = $resValue->ResName;
						$objRes->MrcName = $mrcValue->Name;
						$objRes->MrcCategoryName = $mrcValue->MrcCategoryName;
						$objRes->Position = $resIndex;
						if($resKey >= $maxItemsView) {
							$objRes->ExcludeInitial = true;
						}
						$totalItems[] = $objRes;
						$resIndex++;
					}
				}
			} else {
				$listName = "Resources Search List";
				foreach($items as $mrckey => $mrcValue) {
					$obj = new stdClass();
					$obj->Id = $mrcValue->ResourceId . " - Resource";
					$obj->MerchantId = $mrcValue->MerchantId;
					$obj->MrcCategoryName = $mrcValue->DefaultLangMrcCategoryName;
					$obj->Name = $mrcValue->ResName;
					$obj->MrcName = $mrcValue->MrcName;
					$obj->Position = $mrckey;
					$totalItems[] = $obj;
				}
			}
		}
		$analyticsEnabled = $this->checkAnalytics($listName) && $config->get('eecenabled', 0) == 1;
		if(count($totalItems) > 0 && $analyticsEnabled) {
			$allobjects = array();
			$initobjects = array();
			foreach ($totalItems as $key => $value) {
				$obj = new stdClass;
				$obj->id = "" . $value->Id;
				if(isset($value->GroupId) && !empty($value->GroupId)) {
					$obj->groupid = $value->GroupId;
				}
				$obj->name = $value->Name;
				$obj->category = $value->MrcCategoryName;
				$obj->brand = $value->MrcName;
				$obj->position = $value->Position;
				if(!isset($value->ExcludeInitial) || !$value->ExcludeInitial) {
					$initobjects[] = $obj;
				} else {
					///$obj->merchantid = $value->MerchantId;
					//$allobjects[] = $obj;
				}
			}
			$document->addScriptDeclaration('var currentResources = ' .json_encode($allobjects) . ';
			var initResources = ' .json_encode($initobjects) . ';
			' . ($sendData ? 'callAnalyticsEEc("addImpression", initResources, "list");' : ''));
		}
		
		//event tracking
		
		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		$pagination->setAdditionalUrlParam("searchid", $params['searchid']);
		$pagination->setAdditionalUrlParam("newsearch", 0);
		
		$hidesort = true;
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		$this->assignRef('typologies', $typologies);
		$this->assignRef('config', $config);
		$this->assignRef('hidesort', $hidesort);
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		
		// Display the view
		parent::display($tpl, true);
	}
}
