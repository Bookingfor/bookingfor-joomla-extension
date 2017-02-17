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
class BookingForConnectorViewCondominium extends BFCView
{
//	protected $state = null;
//	protected $item = null;
//	protected $params = null;
//	protected $language = null;	
	
	function display($tpl = null)
	{

//non serve impostare nulla perchè è il pdf engine che richiama la pagina coretta, questo serve solamente per eseguire il PDF component 
// viene usato il "layout" per il documento da elaborare: pdf.php e i suoi template

	}
}
