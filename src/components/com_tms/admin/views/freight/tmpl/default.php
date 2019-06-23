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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('script', Juri::root() . 'media/com_tms/js/tms.js');
?>
<form method="POST" name="adminForm" id="adminForm" class="form-validate container-fluid">
	<div id="tms-wrapper" class="row-fluid">
		<div class="form-horizontal tms-freight">
			<?php
			if (!empty($this->item))
			{
				foreach ($this->item as $destination => $item)
				{
					$freightItems = json_decode($item['box_weight']);
					?>
					<h4 class="center">
						<?php echo ucfirst($destination);?>
					</h4>
					<table class="table table-striped table-bordered">
						<tr>
							<th><?php echo Text::_("COM_TMS_MANAGE_FREIGHT_BOX_WEIGHT");?></td>
							<th><?php echo Text::_("COM_TMS_MANAGE_FREIGHT_FREIGNT");?></td>
							<th><?php echo Text::_("COM_TMS_MANAGE_FREIGHT_INAM");?></td>
							<th><?php echo Text::_("COM_TMS_MANAGE_FREIGHT_ACTION");?></td>
						</tr>
						<?php
						$i = 0;

						// Add empty entry for freight item if there are not any existing entries
						if (empty($freightItems))
						{
							$freightItems = array(array("box_weight" => 0, "freight" => 0, "inam" => 0));
						}

						foreach ($freightItems as $freightItem)
						{
							?>
							<tr class="freight-item-row">
								<td>
									<input type="number"
									class="input-small freight-item-box-weight required"
									name="<?php echo 'freightdata[' . $destination . '][' . $i . '][box_weight]';?>"
									value="<?php echo empty($freightItem->box_weight) ? 0 : $freightItem->box_weight;?>" />
								</td>
								<td>
									<input type="number"
									class="input-small freight-item-freight required"
									name="<?php echo 'freightdata[' . $destination . '][' . $i . '][freight]';?>"
									value="<?php echo empty($freightItem->freight) ? 0 : $freightItem->freight;?>" />
								</td>
								<td>
									<input type="number"
									class="input-small freight-item-inam required"
									name="<?php echo 'freightdata[' . $destination . '][' . $i . '][inam]';?>"
									value="<?php echo empty($freightItem->inam) ? 0 : $freightItem->inam;?>" />
								</td>
								<td>
									<div class="btn-group">
										<a class="group-add btn btn-mini button btn-success" onclick="tms.manageFreight.addFreightEntry(this, '<?php echo $destination;?>');">
											<span class="icon-plus" aria-hidden="true"></span>
										</a>
										<a class="group-remove btn btn-mini button btn-danger" onclick="tms.manageFreight.removeFreightEntry(this);">
											<span class="icon-minus" aria-hidden="true"></span>
										</a>
									</div>
								</td>
							</tr>
							<?php
							$i++;
						}
						?>
					</table>
					<?php
				}
			}
			?>
		<input type="hidden" name="task" value="freight.edit" />
		<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</div>
</form>
