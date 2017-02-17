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
class BookingForConnectorViewCondominiums extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{
		// Initialise variables
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$ordering 	= $state->get('list.ordering');
		$direction 	= $state->get('list.direction');

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		
		$params = $state->params;
		
		// add stylesheet
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		$analyticsEnabled = $this->checkAnalytics("Condominiums List") && $config->get('eecenabled', 0) == 1;
		$pagination->setAdditionalUrlParam("filter_order", $ordering);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $direction);
		
		$this->assignRef('config', $config);
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		
		// Display the view
		parent::display($tpl);
	}
}
