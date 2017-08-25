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
class BookingForConnectorViewMerchantDetails extends BFCView
{
//	protected $state = null;
//	protected $item = null;
//	protected $items = null;
//	protected $pagination = null;
//	protected $document = null;
//	protected $language = null;
			
	// Overwriting JView display method
	function display($tpl = NULL) 
	{
		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		
		$params 	= $state->params;
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
				
		$this->params = $params;
		$this->item = $item;
		$this->document = $document;
		$this->language = $language;
		$this->sitename = $sitename;
		$this->state = $state;
		
		// Display the view
		parent::display($tpl);
	}
}
