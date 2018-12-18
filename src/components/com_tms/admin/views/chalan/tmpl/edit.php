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
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('script', Juri::root() . 'media/com_tms/js/tms.js');

$fieldSetCounter = 0;
$id = isset($this->item->id) ? $this->item->id : 0;
Text::script("COM_TMS_CHALAN_ITEM_LBL");
?>
<script>
	jQuery(document).ready(function (){
		updateTotalWeight();

		jQuery(document).on('subform-row-add', function(event, row){
			jQuery(row).find('select').chosen();
		});

		jQuery('.chalan-paid-details').prepend("<div><b>Third Party Bill-T Paid</b></div><br>");
	});

	function updateTotalWeight()
	{
		let totalWeight = 0;

		jQuery('.subform-repeatable-group').each(function(){
				let units = jQuery(this).find('td .itemUnits').val();
				let weight = jQuery(this).find('td .itemWeight').val();

				if (jQuery.isNumeric(units) && jQuery.isNumeric(weight))
				{
					let tWeight = (units*weight);
					totalWeight = totalWeight+tWeight;
				}
		});

		jQuery('#tms-wrapper #chalan-total-weight h3').html(totalWeight);
	}

	Joomla.submitbutton = function(task)
	{
		jQuery('.subform-repeatable-group').each(function(){
				let emptyRow = 1;
				jQuery(this).find('td .requiredItem').each(function(){
					if (this.value != '')
					{
						emptyRow = 0;

						return false;
					}
				});

				if (emptyRow == 0)
				{
					jQuery(this).find('td .requiredItem').each(function(){
						jQuery(this).attr('required', 'required');
						jQuery("<label class='hidden' for='"+this.id+"'>"+Joomla.JText._('COM_TMS_CHALAN_ITEM_LBL')+"</label>").insertBefore(this);
					});
				}
				else
				{
					jQuery(this).find('td .requiredItem').each(function(){
						jQuery(this).removeAttr('required', 'required');
					});
				}
		});

		if (task == "chalan.apply" || task == "chalan.save")
		{
			var valid = document.formvalidator.isValid(document.getElementById("adminForm"));

			if (valid)
			{
				Joomla.submitform(task, document.getElementById("adminForm"));
			}
		}
		else
		{
			Joomla.submitform(task, document.getElementById("adminForm"));
		}

		if (task == "chalan.print")
		{
			var printContents = document.getElementById('printChalanContent').innerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
		}
	};
</script>
<div id="tms-wrapper">
	<form action="<?php echo 'index.php?option=com_tms&layout=edit&id=' . (int) $id; ?>" method="POST" name="adminForm" id="adminForm" class="form-validate">
		<div class="form-horizontal tms-chalan">
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

				// Iterate through the fields and display them
				foreach ($this->form->getFieldset($fieldset->name) as $field)
				{
					if ($field->name == "jform[chalan_items]")
					{
						?>
						<div class="span6">
							<div class="control-group">
								<div class="control-label">
									<label id="chalan-total-weight-lbl" for="chalan-total-weight"><?php echo Text::_("COM_TMS_CHALAN_ITEM_TOTAL_WEIGHT");?></label>
								</div>
								<div class="controls" id="chalan-total-weight"><h3>0</h3></div>
							</div>
						</div>
						<div class="chalan-details"><?php echo $this->form->getInput('chalan_items'); ?></div>
						<?php
					}
					elseif ($field->name == "jform[third_party_paid]")
					{
						?>
						<div class="span6">
							<div class="chalan-paid-details"><?php echo $this->form->getInput('third_party_paid'); ?></div>
						</div>
						<?php
					}
					else
					{
						?>
						<div class="<?php echo ($field->type != 'Hidden') ? 'span6' : '';?>">
						<?php echo $field->renderField(); ?>
						</div>
						<?php
					}
				}

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
		<input type="hidden" name="task" value="chalan.edit" />
		<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
	<?php
	if (!empty($this->item->id))
	{
	?>
	<div id="printChalanContent" class="d-none">
		<?php
			$layout = new JLayoutFile('chalan_print', $basePath = JPATH_SITE . '/administrator/components/com_tms/layouts/chalan');
			echo $layout->render($this->form);
		?>
	</div>
	<?php
	}
	?>
</div>
