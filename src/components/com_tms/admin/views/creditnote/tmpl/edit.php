<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select', null, array('disable_search_threshold' => 0 ));

$fieldSetCounter = 0;
$id = isset($this->item->id) ? $this->item->id : 0;
?>
<div id="tms-wrapper" class="container-fluid">
	<form action="<?php echo 'index.php?option=com_tms&layout=edit&id=' . (int) $id; ?>" method="POST" name="adminForm" id="adminForm" class="form-validate">
		<div class="alert alert-info"><?php echo Text::_("COM_TMS_TRANSACTION_ADD_CREDIT_NOTE_INFO");?></div>
		<div class="form-horizontal">
		<?php
		if ($this->form)
		{
			// Iterate through the form fieldsets and display each one
			$fieldSets = $this->form->getFieldsets();

			foreach ($fieldSets as $fieldset)
			{
				// If more than one field sets are added in a form
				if (count($fieldSets) > 1)
				{
					if ($fieldSetCounter == 0)
					{
						$firstTabName = OutputFilter::stringURLUnicodeSlug(trim($fieldset->name));
						echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => $firstTabName));
					}

					$fieldSetCounter ++;

					// Tab name
					$tabName = OutputFilter::stringURLUnicodeSlug(trim($fieldset->name));

					// Create tab for fieldset
					echo HTMLHelper::_("bootstrap.addTab", "myTab", $tabName, $fieldset->name);
				}
				?>
				<div class="row-fluid">
				<?php
				// Iterate through the fields and display them
				foreach ($this->form->getFieldset($fieldset->name) as $field)
				{
					?>
					<div class="span6">
						<?php echo $field->renderField();?>
					</div>
					<?php
				}
				?>
				</div>
				<?php
				if (count($this->form->getFieldsets()) > 1)
				{
					echo HTMLHelper::_("bootstrap.endTab");
				}
			}

			if (count($fieldSets) > 1)
			{
				echo HTMLHelper::_('bootstrap.endTabSet');
			}
		}
		?>
		</div>
		<input type="hidden" name="task" value="transaction.edit" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
