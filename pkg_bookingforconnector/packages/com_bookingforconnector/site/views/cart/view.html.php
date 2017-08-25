<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

class BookingForConnectorViewCart extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $actionmode = null;

		
	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false)
	{

		// Initialise variables
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$document 	= JFactory::getDocument();

		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
				
		$this->state = $state;		
		$this->language = $language;
		$this->items = $items;
		$this->sitename = $sitename;
		$analyticsEnabled = $this->checkAnalytics("");
		$this->analyticsEnabled = $analyticsEnabled;

		
		// Display the view
		parent::display($tpl, true);
	}
		
}
