<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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

	// Overwriting JView display method
	function display($tpl = null) 
	{
	if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
		$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
		foreach( $listCrawler as $key=>$crawler){
		if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) return;
		}
		
	}


		// Initialise variables
		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		JHTML::stylesheet('components/com_bookingforconnector/assets/js/slick/slick.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/js/slick/slick-theme.css');
		JHTML::script('components/com_bookingforconnector/assets/js/slick/slick.min.js');
		
		bfi_setSessionFromSubmittedData();

		$state		= $this->get('State');
		$params = $state->params;
		if(!isset($params) || empty($params['checkin']) ){
			header ("Location: ". JURI::root()); 
			$app->close();
		}
		$altsearch = BFCHelper::getVar('altsearch','0');
		$items		= null;
		$pagination	= new JPagination(0, $state->get('list.start'), $state->get('list.limit') );

		if (empty($altsearch)) {
			$items		= $this->get('Items');
			$pagination	= $this->get('Pagination');
		    
		}
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		$currParam = BFCHelper::getSearchParamsSession();
		$merchantResults = $currParam['merchantResults'];
		$condominiumsResults = $currParam['condominiumsResults'];
		$totPerson = $currParam['paxes'];

		/*-- criteo --*/
		$criteoConfig = null;
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
				$merchantsCriteo = array();
				if(!empty($items)) {
					$merchantsCriteo = array_unique(array_map(function($a) { return $a->MerchantId; }, $items));
				}
				$criteoConfig = BFCHelper::getCriteoConfiguration(1, $merchantsCriteo);
				if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
					JHTML::script('//static.criteo.net/js/ld/ld.js');
					$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "viewSearch", checkin_date: "' . $currParam["checkin"]->format('Y-m-d') . '", checkout_date: "' . $currParam["checkout"]->format('Y-m-d') . '"},
						{ event: "viewList", item: ' . json_encode($criteoConfig->merchants) .' }
					);');
				}
		}
	
		$totalItems = array();
		$listName = "";
		$listNameAnalytics = 0;
		$sendData = true;
		
		
		if(!empty($items)) {
			if($merchantResults) {
//				$resIndex = 0;
				$listNameAnalytics = 1;
				$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];//"Merchants Group List";
				foreach($items as $itemkey => $itemValue) {
//					$obj = new stdClass();
//					$obj->Id = $itemValue->MerchantId . " - Merchant";
//					$obj->MerchantId = $itemValue->MerchantId;
//					$obj->Name = $itemValue->MrcName;
//					$obj->MrcCategoryName = $itemValue->MrcCategoryName;
//					$obj->MrcName = $itemValue->MrcName;
//					$obj->Position = $itemkey;
//					$totalItems[] = $obj;
					$objRes = new stdClass();
					$objRes->Id = $itemValue->ResourceId . " - Resource";
					$objRes->MerchantId = $itemValue->MerchantId;
					$objRes->Name = $itemValue->ResName;
					$objRes->MrcName = $itemValue->MrcName;
					$objRes->MrcCategoryName = $itemValue->MrcCategoryName;
					$objRes->Position = $itemkey;// $resIndex;
					$totalItems[] = $objRes;
				}
			} else if ($condominiumsResults) {
//				$sendData = false;
				$resIndex = 0;
				$listNameAnalytics = 2;
				$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Group List";
				foreach($items as $itemkey => $itemValue) {
//					$obj = new stdClass();
//					$obj->Id = $mrcValue->CondominiumId . " - Resource Group";
//					$obj->MerchantId = $mrcValue->MerchantId;
//					$obj->Name = $mrcValue->Name;
//					$obj->MrcCategoryName = $mrcValue->MrcCategoryName;
//					$obj->MrcName = $mrcValue->MerchantName;
//					$obj->Position = $mrckey;
//					$totalItems[] = $obj;
					$objRes = new stdClass();
					$objRes->Id = $itemValue->ResourceId . " - Resource";
					$objRes->CondominiumId= $itemValue->CondominiumId;
					$objRes->MerchantId = $itemValue->MerchantId;
					$objRes->Name = $itemValue->ResName;
					$objRes->MrcName = $itemValue->MrcName;
					$objRes->MrcCategoryName = $itemValue->MrcCategoryName;
					$objRes->Position = $itemkey;// $resIndex;
					$totalItems[] = $objRes;
				}
			} else {
				$listNameAnalytics = 3;
				$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";
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

		$analyticsEnabled = $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		
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
		if ($pagination!=null) {
			$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
			$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
			$pagination->setAdditionalUrlParam("searchid", $params['searchid']);
			$pagination->setAdditionalUrlParam("newsearch", 0);
		    
		}
		
		$this->state = $state;
		$this->params = $params;
		$this->items = $items;
		$this->totalAvailable = $this->get('TotalAvailable');

		$this->pagination = $pagination;
		$this->language = $language;
//		$this->typologies = $typologies;
		$this->config = $config;
		$this->hidesort = true;
		$this->analyticsEnabled = $analyticsEnabled;
		$this->listNameAnalytics = $listNameAnalytics;
		
		// Display the view
		parent::display($tpl);
	}
}
