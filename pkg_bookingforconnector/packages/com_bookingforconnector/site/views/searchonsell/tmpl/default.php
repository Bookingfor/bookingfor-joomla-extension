<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.module.helper');

$params = $this->params;
$language = $this->language;
$showmap = true;
$config = $this->config;
$isportal = $config->get('isportal', 1);
$enablescalarrequest = $config->get('enablescalarrequest', 1);

$total = $this->pagination->total;

$mainframe = JFactory::getApplication();
$pathway   = $mainframe->getPathway();
// resetto il pathway				
$pathway->setPathway(null);

$pathway->addItem(
	JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SEARCHONSELL_PATHWAY'), ""
);

if($total<1){
	$showmap = false;
}
?>
<div id="bfcmerchantlist">
<div id="com_bookingforconnector-items-container-wrapper">
 	<?php if ($total > 0): ?>
		<div class="com_bookingforconnector-items-container">
			<?php
			echo  $this->loadTemplate('resources'); 
				if ($showmap) {
				  echo  $this->loadTemplate('googlemap');
					echo  $this->loadTemplate('googlemap_resources');
				}
			?>
				<?php if ($this->pagination->get('pages.total') > 1) : ?>
					<div class="text-center">
					<div class="pagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
					</div>
				<?php endif; ?>
		</div>
	<script type="text/javascript">
	<!--
	var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
	var cultureCode = '<?php echo $language ?>';

	jQuery(document).ready(function(){
		jQuery("#titlemast").html("<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_SEARCHRESULTS_TITLE_SMALL'), $total) ?>"); 

	});
	//-->
	</script>	
	<?php else: ?>
		<div class="com_bookingforconnector_search-noresults">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_NORESULTS') ?>
				<?php 
				//echo  $this->loadTemplate('resources'); 
				if  ($enablescalarrequest == true) {
					echo  $this->loadTemplate('scalarrequest');
				}
				?>
		</div>
	<?php endif; ?>
</div>
</div>
