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
//		$pagination	= $this->get('Pagination');
		$document 	= JFactory::getDocument();

		// load scripts
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');

		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		

		//load scripts wizard
		$document->addScript('components/com_bookingforconnector/assets/js/bbq.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.wizard.js');

		// Initialise variables

//		$params = $state->params;


		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
				
		$this->assignRef('state', $state);		
//		$this->assignRef('params', $params);
		$this->assignRef('language', $language);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('sitename', $sitename);

		
		// Display the view
		parent::display($tpl, true);
	}
		
}
