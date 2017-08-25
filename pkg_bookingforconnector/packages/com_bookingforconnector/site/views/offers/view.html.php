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

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		

		// Initialise variables
		$state		= $this->get('State');
		$params = $state->params;
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
//		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
//		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		
		$this->state = $state;
		$this->language = $language;
		$this->params = $params;
		$this->items = $items;
		$this->pagination = $pagination;
		
		// Display the view
		parent::display($tpl);
	}
}
