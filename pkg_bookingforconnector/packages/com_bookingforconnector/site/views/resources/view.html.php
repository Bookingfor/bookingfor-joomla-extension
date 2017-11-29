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
class BookingForConnectorViewResources extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null) 
	{

		// Initialise variables
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$ordering 	= $state->get('list.ordering');
		$direction 	= $state->get('list.direction');

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		
		$params = $state->params;
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		$listNameAnalytics =5;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";

		$analyticsEnabled = count($items) > 0 && $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;

		$pagination->setAdditionalUrlParam("filter_order", $ordering);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $direction);
		
		$this->state = $state;
		$this->params = $params;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->language = $language;
		$this->analyticsEnabled = $analyticsEnabled;
		$this->listNameAnalytics = $listNameAnalytics;
		
		// Display the view
		parent::display($tpl);
	}
}
