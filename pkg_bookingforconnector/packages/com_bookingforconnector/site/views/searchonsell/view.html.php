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
class BookingForConnectorViewSearchOnSell extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;
	protected $typologies = null;

	// Overwriting JView display method
	function display($tpl = NULL) 
	{
		// Initialise variables
		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$state		= $this->get('State');
		$params = $state->params;
		$hashunsubscribe = BFCHelper::getVar('hash');
		// controllo per eventuale redirect in caso la pagina sia chiamata ma non ci siano parametri corretti
		if(!isset($params) && empty($hashunsubscribe) ){
			header ("Location: ". JURI::root()); 
			$app = JFactory::getApplication();
			$app->close();
		}
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

//		$typologies = $this->getModel()->getMasterTypologies(true,$language);
		bfi_setSessionFromSubmittedData();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		

		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		$pagination->setAdditionalUrlParam("searchid", $params['searchid']);
		if(!empty($params['zoneIds'])){
			$pagination->setAdditionalUrlParam("zoneIds", $params['zoneIds']);
		}
		if(!empty($params['zoneId'])){
			$pagination->setAdditionalUrlParam("zoneId", $params['zoneId']);
		}
				
		$listNameAnalytics = 8;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";
		$analyticsEnabled = count($items) > 0 && $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;

		$analyticsEnabled = $this->checkAnalytics($listName);
		if(count($items) > 0 && $analyticsEnabled && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$allobjects = array();
			foreach ($items as $key => $value) {
				$obj = new stdClass;
				$obj->id = "" . $value->ResourceId . " - Sales Resource";
				$obj->name = $value->Name;
				$obj->category = $value->MerchantCategoryName;
				$obj->brand = $value->MerchantName;
				$obj->position = $key;
				$allobjects[] = $obj;
			}
			$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' .json_encode($allobjects) . ', "list");');
		}
				
		$this->state = $state;
		$this->params = $params;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->document =  $document;
		$this->language = $language;
		$this->hashunsubscribe = $hashunsubscribe;
//		$this->typologies = $typologies;
		$this->config = $config;
		$this->analyticsEnabled = $analyticsEnabled;
		$this->listNameAnalytics = $listNameAnalytics;
		
		// Display the view
		parent::display($tpl);
	}
}
