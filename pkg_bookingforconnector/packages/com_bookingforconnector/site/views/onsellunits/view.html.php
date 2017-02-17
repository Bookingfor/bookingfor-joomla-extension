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
class BookingForConnectorViewOnSellUnits extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		

		// add stylesheet
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');

		// Initialise variables
		$state		= $this->get('State');
		$params = $state->params;
		

		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');
		$currentLayout = BFCHelper::getString('layout');

		if ($currentLayout == 'favorites') {
				$items		= $this->get('ItemsFavorites');

		}else{
			$show_latest = BFCHelper::getVar('show_latest',0);
			$params['show_latest'] = $show_latest;
		
	//		$isportal = BFCHelper::getString('isportal');
			$isportal = $config->get('isportal', 1);

			
//			if(!$isportal){
//				$this->setLayout("merchantdefault");
//			}
			if($params['show_latest']){
				$items		= $this->get('ItemsLatest');
				$pagination	= $this->get('PaginationLatest');
			}else{
				$items		= $this->get('Items');
				$pagination	= $this->get('Pagination');
			}
			$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
			$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
			$pagination->setAdditionalUrlParam("locationzones", $params['locationzones']);

		}

		
		if(count($items) && $this->checkAnalytics('Sales Resource List' . ($params['show_latest'] ? ' - Latest' : '')) && $config->get('eecenabled', 0) == 1) {
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
			$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		$this->assignRef('config', $config);
		
		// Display the view
		parent::display($tpl);
	}
}
