<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class BookingForConnectorViewOrders extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $params = null;
	protected $language = null;
	protected $actionform = null;
	
	// Overwriting JView display method
	function display($tpl = NULL)
	{

		$document 	= JFactory::getDocument();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');

		// Initialise variables

		$state		= $this->get('State');
		$params = $state->params;
		$language 	= $document->getLanguage();
				
		$this->assignRef('state', $state);		
		$this->assignRef('params', $params);
		$this->assignRef('language', $language);		
		$this->assignRef('sitename', $sitename);
		$this->assignRef('config', $config);

		
		// Display the view
		parent::display($tpl);
	}
}
