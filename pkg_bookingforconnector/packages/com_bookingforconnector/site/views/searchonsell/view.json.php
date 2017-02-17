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
	protected $params = null;

	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false) 
	{
		// Initialise variables
		$state		= $this->get('State');
		//$items		= $this->get('Items');
		$model = $this->getModel();
		$items = $model->getItems(true,true);
		$params = $state->params;
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);

		// Display the view
		$this->setLayout('json');
		parent::display($tpl);
	}
}
