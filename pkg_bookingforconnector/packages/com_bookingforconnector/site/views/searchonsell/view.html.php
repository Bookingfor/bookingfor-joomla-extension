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
class BookingForConnectorViewSearchOnSell extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;
	protected $typologies = null;

	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false) 
	{
		// Initialise variables
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
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

		$typologies = $this->getModel()->getMasterTypologies(true,$language);

		
		// add stylesheet
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');

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
				
		$analyticsEnabled = $this->checkAnalytics("Sales Resource List");
		if(count($items) > 0 && $analyticsEnabled && $config->get('eecenabled', 0) == 1) {
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
				
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('hashunsubscribe', $hashunsubscribe);
		$this->assignRef('typologies', $typologies);
		$this->assignRef('config', $config);
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		
		// Display the view
		parent::display($tpl, true);
	}
}
