<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$currencyclass = bfi_get_currentCurrency();

$resource = $this->item;
$merchant = $resource->Merchant;
$resourceId = 0;
$condominiumId = $resource->condominiumId;
$language = $this->language;


$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

BFCHelper::bfi_get_template("shared/search_details.php",array("resource"=>$resource,"merchant"=>$merchant,"resourceId"=>$resourceId,"condominiumId"=>$condominiumId,"currencyclass"=>$currencyclass));	
?>