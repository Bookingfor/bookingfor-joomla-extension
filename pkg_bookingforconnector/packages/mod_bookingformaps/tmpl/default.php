<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
?>
<div class="mod_bookingformaps-static">
<span class="showmap"><?php echo JTEXT::_('MOD_BOOKINGFORMAPS_SHOW_MAP')?></span>
<img alt="Map" src="https://maps.google.com/maps/api/staticmap?center=<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSY?>,<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSX?>&amp;zoom=11&amp;size=400x400&key=<?php echo $googlemapsapykey ?>&" style="max-width: 100%;">
</div>
<div id="mod_bookingformaps-popup">
</div>
