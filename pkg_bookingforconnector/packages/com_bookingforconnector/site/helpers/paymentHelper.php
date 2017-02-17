<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';
require_once $pathbase . '/helpers/payments/keyclient.php';
require_once $pathbase . '/helpers/payments/setefi.php';
require_once $pathbase . '/helpers/payments/bnlpositivity.php';
require_once $pathbase . '/helpers/payments/wspayform.php';
require_once $pathbase . '/helpers/payments/pagoonline.php';
require_once $pathbase . '/helpers/payments/activa.php';
require_once $pathbase . '/helpers/payments/virtualpay.php';
require_once $pathbase . '/helpers/payments/paypalexpress.php';
require_once $pathbase . '/helpers/payments/bankart.php';


class baseProcessor{
	public function __construct($order,$url, $debug = FALSE)
	{

	}

	
	public function getResult($param=null) {
		return false;
	}
}
