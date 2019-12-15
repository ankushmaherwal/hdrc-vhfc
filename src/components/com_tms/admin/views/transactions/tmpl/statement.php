<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
$base = JUri::base();

JFactory::getDocument()->addScriptDeclaration('
	jQuery(document).ready(function(){
		jQuery("#adminForm select").each(function(index) {
			jQuery(this).removeAttr("onchange");
		});
	});

	var generateStatement = function (){
		if (jQuery("#filter_account_id").val() == "")
		{
			jQuery("#filter_account_id").css("border-color", "red");
		}
		else
		{
			jQuery("#filter_account_id").css("border-color", "");
			document.getElementById("adminForm").submit();
		}

		jQuery("html, body", window.parent.document).animate({ scrollTop: 0 }, "slow");
	}
');
?>
<form action="index.php?option=com_tms&view=transactions" method="post" id="adminForm" name="adminForm">
	<div id="tms-wrapper">
		<h1><?php echo Text::_("COM_TMS_TRANSACTION_SELECT_ACCOUNT_STATEMENT");?></h1>
		<hr/>
		<div class="row">
			<?php
			foreach ($this->filterForm->getGroup('filter') as $field)
			{
				if (in_array($field->id, array('filter_account_id', 'filter_from_date', 'filter_to_date', 'filter_category_id')))
				{
					?>
					<div class="span3">
						<?php echo $field->renderField();?>
					</div>
					<?php
				}
				?>
				<?php
			}
			?>
		</div>
		<div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div>
		<div class="center">
			<a href="#" class="btn btn-success" onclick="generateStatement();"><?php echo Text::_("COM_TMS_TRANSACTION_PRINT");?></a>
		</div>
		<input type="hidden" name="option" value="com_tms" />
		<input type="hidden" name="task" value="transactions.generateStatement" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
