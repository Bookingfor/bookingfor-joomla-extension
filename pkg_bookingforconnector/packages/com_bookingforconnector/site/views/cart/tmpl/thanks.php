<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language 	= $this->language;
$tmpUserId = BFCHelper::bfi_get_userId();
$currCart = BFCHelper::GetCartByExternalUser($tmpUserId, $language, true);
?>
<div class="bfi-content">
<br />
	<h2><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_THANKS')?></h2>
<br />
<br />
</div>