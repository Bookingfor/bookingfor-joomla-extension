<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
//jimport('joomla.application.component.controller');
 
/**
 * General Controller of HelloWorld component
 */
class BookingForConnectorController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// set default view if not set
		$view   = $this->input->get('view', 'BookingForConnector');
		$this->input->set('view', $view);
		// call parent behavior
		parent::display($cachable);
	}
}
