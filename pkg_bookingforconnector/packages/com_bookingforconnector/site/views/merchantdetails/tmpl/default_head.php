<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$name = BFCHelper::getLanguage($this->item->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
?>
	<h2 class="com_bookingforconnector_merchant-name"><?php echo  $name?> 
		<span class="com_bookingforconnector_resource-merchant-rating">
		  <?php for($i = 0; $i < $this->item->Rating; $i++) { ?>
		  <i class="fa fa-star"></i>
		  <?php } ?>
		</span>
	</h2>
