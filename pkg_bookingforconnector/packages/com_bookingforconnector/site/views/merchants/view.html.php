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
class BookingForConnectorViewMerchants extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();

		// add stylesheet
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');
		$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');

		// load scripts
		$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');

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
		
		$analyticsEnabled = count($items) > 0 && $this->checkAnalytics("Merchants List") && $config->get('eecenabled', 0) == 1;
		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		$pagination->setAdditionalUrlParam("startswith", $startswith);
		$pagination->setAdditionalUrlParam("searchseed", $searchseed);
		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		$this->assignRef('config', $config);
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		
		// Display the view
		parent::display($tpl);
	}
}
