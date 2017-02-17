<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$params = $this->params;
$document = $this->document;
$language 	= $this->language;

$user = JFactory::getUser();
if (!$user->guest)
{
	echo  $this->loadTemplate('user'); 
}

?>   
<!-- {emailcloak=off} -->

