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
	function display($tpl = NULL, $preparecontent = false)
	{

		$document 	= JFactory::getDocument();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');

		// load scripts
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.maskedinput.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.additional-custom-methods.js');
		
		// Initialise variables

		$state		= $this->get('State');
		
		$actionform = BFCHelper::getVar('actionform',"");
		
		if ($actionform=="login"){
			$item = $this->get('Item'); 
			/*$item = "fet";*/
		}
		
		//inserimeto dell'email dell'ordine altrimnenti non si può continuare con i pagamenti 
		if ($actionform=="insertemail"){
			$orderId = BFCHelper::getVar('orderId',"");
			$email = BFCHelper::getVar('email',"");
			$model = $this->getModel();
			$item = $model->updateEmail($orderId,$email); 
			/*$item = "fet";*/
		}
		
		
		$params = $state->params;


		$language 	= $document->getLanguage();
				
		$this->assignRef('state', $state);		
		$this->assignRef('params', $params);
		$this->assignRef('language', $language);		
		$this->assignRef('item', $item);
		$this->assignRef('sitename', $sitename);
		$this->assignRef('config', $config);

		$this->assignRef('actionform', $actionform);
		/*
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$pagination->setAdditionalUrlParam("filter_order", $ordering);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $direction);



		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		*/

		
		// Display the view
		parent::display($tpl, true);
	}
}
