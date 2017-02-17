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

class BookingForConnectorViewTag extends BFCView
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
		$item		= $this->get('Item');
		$params 	= $state->params;
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');
		
		$this->assignRef('state', $state);		
		$this->assignRef('language', $language);
		$this->assignRef('item', $item);		
		$this->assignRef('sitename', $sitename);
		$this->assignRef('config', $config);
		$category = 0;

		if(!empty($item)) {
			$category = $item->SelectionCategory;
			
			$show_grouped = BFCHelper::getVar('show_grouped',0);
			$params['show_grouped'] = $show_grouped;
			$params['onlystay'] = 'false';


			if ($category  == 1) {
				$items = $this->get('ItemsMerchants');
				$pagination	= $this->get('Pagination');
			}
			if ($category == 2) {
				$items = $this->get('ItemsOnSellUnit');
				$pagination	= $this->get('Pagination');
			}
			if ($category == 4) {
				$items = $this->get('ItemsResources');
				$pagination	= $this->get('Pagination');
			}

			$this->assignRef('items', $items);
			$this->assignRef('pagination', $pagination);
			
			$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
			$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
			$pagination->setAdditionalUrlParam("newsearch",0);
		}
		$this->assignRef('category', $category);		
		$this->assignRef('params', $params);
		
	
		
		
		JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');

		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.ui.touch-punch.min.js');

		// Initialise variables

				
//		$params = $state->params;


				

		
		// Display the view
		parent::display($tpl, true);
	}
		
}
