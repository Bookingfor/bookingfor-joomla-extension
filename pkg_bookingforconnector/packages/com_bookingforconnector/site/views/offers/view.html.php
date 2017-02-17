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
class BookingForConnectorViewOffers extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{
		// add stylesheet
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		// Initialise variables
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		
		$params = $state->params;
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');

		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		
		// Display the view
		parent::display($tpl);
	}
}
