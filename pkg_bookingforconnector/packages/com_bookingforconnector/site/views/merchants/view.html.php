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
class BookingForConnectorViewMerchants extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null) 
	{

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		

		// Initialise variables
		$state		= $this->get('State');
		$params = $state->params;
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');
		$startswith 	= $params['startswith'];
		$searchseed 	= $params['searchseed'];

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		$merchantsCriteo = isset($items) && !empty($items) ? array_unique(array_map(function($a) { return $a->MerchantId; }, $items)) : array();
		$criteoConfig = BFCHelper::getCriteoConfiguration(1, $merchantsCriteo);
		if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
			$document->addScript('//static.criteo.net/js/ld/ld.js');
			$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
			window.criteo_q.push( 
				{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
				{ event: "setSiteType", type: "d" }, 
				{ event: "setEmail", email: "" }, 
				{ event: "viewList", item: '. json_encode($criteoConfig->merchants) .' }
			);');
		}
		
//		$analyticsEnabled = count($items) > 0 && $this->checkAnalytics("Merchants List") && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		$listNameAnalytics =4;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";

		$analyticsEnabled = $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		$altsearch = BFCHelper::getVar('altsearch','0');
		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		$pagination->setAdditionalUrlParam("startswith", $startswith);
		$pagination->setAdditionalUrlParam("searchseed", $searchseed);
		$pagination->setAdditionalUrlParam("newsearch", 0);
		
		$this->state = $state;
		$this->language = $language;
		$this->params = $params;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->analyticsEnabled = $analyticsEnabled;
		$this->startswith = $startswith;
		$this->altsearch = $altsearch;
		$this->listNameAnalytics = $listNameAnalytics;

		// Display the view
		parent::display($tpl);
	}
}
