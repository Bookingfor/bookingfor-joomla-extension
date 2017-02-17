<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$merchant = $this->item;
$sitename = $this->sitename;
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_PACKAGES_TITLE'),$merchant->Name,$sitename));

$config = $this->config;
$isportal = $config->get('isportal', 1);
$showdata = $config->get('showdata', 1);

$total = $this->pagination->total;

$action = htmlspecialchars(JFactory::getURI()->toString());
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
//$searchid =  $this->params['searchid'];
$searchid ="";
$activePrice="";
$activepackage="";
$hidesort = "";
//if(!empty($this->hidesort)) {
//	$hidesort = ' style="display:none;"';
//}

?>

<div id="com_bookingforconnector-items-container-wrapper">
	<div class="com_bookingforconnector-items-container">
		<h1 class="com_bookingforconnector_search-title"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_PACKAGES_TITLE_TOTAL'), $total) ?></h1>

	<div class="com_bookingforconnector-search-menu">
		<!--
		<form action="<?php echo $action; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
			<fieldset class="filters">
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
				<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
				<input type="hidden" name="limitstart" value="0" />
			</fieldset>
		</form>
		<div class="com_bookingforconnector-results-sort">
			<span class="com_bookingforconnector-sort-help"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
			<span class="com_bookingforconnector-sort-item<?php echo $activePrice; ?>" rel="stay|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_PRICE'); ?></span>
			<span class="com_bookingforconnector-sort-item<?php echo $activepackage; ?>" rel="package|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_packageS'); ?></span>
		</div>
		-->
		<div class="com_bookingforconnector-view-changer">
			<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
		</div>
	</div>
	
	<div class="clearfix"></div>
	
	<div class="com_bookingforconnector-search-resources com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
	
		<?php if ($this->items != null): ?>
			<?php foreach($this->items as $key => $package): ?>
			<?php
			// assign the current package to a property so it will be available inside template 'package'
			$this->item->currentOffer = $package; 
			$this->item->currentIndex = $key;
			?>
			<?php echo  $this->loadTemplate('package'); ?>
			<?php endforeach?>
			<?php if ($this->pagination->get('pages.total') > 1) : ?>
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php endif; ?>
		<?php else:?>
		<div class="com_bookingforconnector_merchantdetails-nopackages">
			<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_NORESULT')?>
		</div>
		<?php endif?>
	
	</div>
	</div>
</div>

<script type="text/javascript">
<!--
jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2');
	localStorage.setItem('display', 'list');
})

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	localStorage.setItem('display', 'grid');
})

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfc_display === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfc_display == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}

	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};
jQuery(document).ready(function() {
	jQuery(".com_bookingforconnector-item-primary-description").shorten(shortenOption);
});

//-->
</script>
