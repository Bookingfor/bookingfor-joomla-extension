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
$uri  = 'index.php?option=com_bookingforconnector&view=search';
$formAction = JRoute::_($uri);
$session = JFactory::getSession();
$limitstart = BFCHelper::getInt('limitstart', 0);
//$newsearch = 0;
$newsearch = BFCHelper::getInt('newsearch', 0);

if($limitstart ==0 && $newsearch !=0){
	$newsearch = 2;
}
?>
<div class="mod_bookingforsearchfilter<?php echo $moduleclass_sfx ?>">
<h3><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_TITLE') ?></h3>
<div id="searchfilterloading" style="min-height:60px;">
<form method="post" action="<?php echo $formAction ?>" id="searchformfilterloading">
	<input type="hidden" id="format" value="raw" />
	<input type="hidden" id="layout" value="filters" />
	<input type="hidden" id="tmpl" value="component" />
	<input type="hidden" id="onlystay" value="1" />
	<input type="hidden" id="newsearch" value="<?php echo $newsearch ?>" />
</form>
</div>
</div>
<script type="text/javascript">
//jQuery(document).ready(function(){
////jQuery("#searchformfilterloading" ).ajaxSubmit({
////			target:     '#searchfilterloading',
////			replaceTarget: true, 
////			beforeSend: function() {
////				jQuery('#searchfilterloading').block();
////			},
////			success:  function() {
////				jQuery('#searchfilterloading').unblock();
////				applyfilterdata();
////			},
////			error:  function() {
////				jQuery('#searchfilterloading').unblock();
////			},
////	});
////
//}); 
var callfilterloading = function callfilterloadingAnony() {
	jQuery('#searchfilterloading').block({css: { 
        padding:        0, 
        margin:         0, 
        width:          '90%', 
        top:            '40%', 
        left:           '35%', 
        textAlign:      'center', 
        color:          '#000', 
        border:         '3px solid #aaa', 
        backgroundColor:'#fff', 
        cursor:         'wait' 
    }, message:'loading' 
	});
	jQuery("#searchfilterloading" ).load( '', { "format":"raw","layout": "filters","tmpl": "component","onlystay": "1","newsearch": "<?php echo $newsearch ?>"} , function() {
		applyfilterdata();
		jQuery('#searchfilterloading').unblock();
	});  
};
</script>
