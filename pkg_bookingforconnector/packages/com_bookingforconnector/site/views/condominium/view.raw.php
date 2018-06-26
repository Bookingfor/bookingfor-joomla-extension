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
class BookingForConnectorViewCondominium extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $gotCalculator = null;
	protected $stay = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false)
	{
		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');

		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();
		$params = $state->params;
		if (BFCHelper::getString('layout') == 'resourcesajax') {
			$items = $this->get('ItemsResourcesAjax');
		}
		if (BFCHelper::getString('layout') == 'resources') {
			$items = $this->get('Items');
			$pagination	= $this->get('Pagination');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
				
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('items', $items);
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		
		// Display the view
		//parent::display($tpl);
		parent::display($tpl, true);
	}
	
}
