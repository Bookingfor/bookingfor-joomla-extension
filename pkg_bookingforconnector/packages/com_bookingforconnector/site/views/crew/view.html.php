<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/paymentHelper.php';

class BookingForConnectorViewCrew extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $actionmode = null;

		
	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false)
	{

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();

		$document->addStyleSheet('components/com_bookingforconnector/assets/css/font-awesome.min.css');
//		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bootstrap.min.css');
		$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');

		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		

		//load scripts wizard
		$document->addScript('components/com_bookingforconnector/assets/js/bbq.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.wizard.js');
		
		$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
		if(substr($language,0,2)!='en'){
//			$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
			$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
		}

		// Initialise variables

		$state		= $this->get('State');
		
		$actionform = BFCHelper::getVar('actionform',"");
		
		$item = $this->get('Item');
		
		if ($actionform=="insert"){
			$item = $this->getModel()->setCrew();
			/*$item = $this->get('Item');*/ 
		}
		
		$params = $state->params;


				
		$this->assignRef('state', $state);		
		$this->assignRef('params', $params);
		$this->assignRef('language', $language);
		$this->assignRef('actionform', $actionform);		
		$this->assignRef('item', $item);

		
		// Display the view
		parent::display($tpl, true);
	}
		
}
