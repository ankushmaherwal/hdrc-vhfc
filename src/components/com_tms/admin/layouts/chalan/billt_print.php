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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

if (empty($displayData))
{
	return false;
}

$params = ComponentHelper::getParams('com_tms');
$chalanItem = $displayData;
$db = Factory::getDbo();
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');
$vehicleTable = Table::getInstance('Vehicle', 'TmsTable', array('dbo', $db));

if (!empty($chalanItem))
{
	if (!empty($chalanItem['chalan_id']))
	{
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/administrator/components/com_tms/models');
		$chalanModel = BaseDatabaseModel::getInstance('Chalan', 'TmsModel');
		$chalanData = $chalanModel->getItem($chalanItem['chalan_id']);

		$vehicleTable->load(array('id' => $chalanData->vehicle_id));
		$vehicle_number = $vehicleTable->registration_number;
	}
	?>
	<div style="padding:15px;border-style:ridge;" class="chalan-print">
		<div>
			<table>
				<tr style="text-align:center;">
					<td style="width:15%;">
						<h2 style="border-style:solid;border-radius:20px;"><?php echo Text::_($params->get('ch_company_name_short', 'XXX'));?></h2>
					</td>
					<td colspan="4">
						<h1><?php echo Text::_($params->get('ch_company_name', 'XXX'));?></h1>
						<h4><?php echo Text::_($params->get('ch_company_address', 'XXX'));?></h4>
						<h4><?php echo Text::sprintf("COM_TMS_PRINT_CHALAN_CONTACT_NUMBER", $params->get('ch_contact_number', 'XXX'));?></h4>
					</td>
				</tr>
				<tr style="text-align:center;">
					<td>
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_BILLT_SNO")?></span>
						<span><?php echo sprintf('%05d', $chalanItem['id']);?></span>
					</td>
					<td>
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_DATE")?></span>
						<span><?php echo HTMLHelper::date($chalanData->date, Text::_('COM_TMS_DISPLAY_DATE_FORMAT'));?></span>
					</td>
					<td colspan="2" style="width:30%;">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_TRUCK_NUMBER")?></span>
						<span><?php echo $vehicle_number;?></span>
					</td>
					<td>
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_CHALAN_DESTINATION")?></span>
						<span><?php echo $chalanData->destination;?></span>
					</td>
				</tr>
			</table>
		</div>
		<div>
			<table style="width:100%;">
				<tr>
					<td style="width:70%;">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_CHALAN_ITEM_SENDER_PARTY")?></span>
						<?php
						$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
						$accountTable->load(array('id' => $chalanItem['sender_party']));
						?>
						<span><?php echo " : " . $accountTable->title;?></span>
					</td>
					<td style="width:30%;text-align:center;">
						<span style="font-weight: bold;"><?php echo Text::_("Chalan Ref.")?></span>
						<span><?php echo sprintf('%05d', $chalanItem['chalan_id']);?></span>
					</td>
				</tr>
				<tr>
					<td>
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_CHALAN_ITEM_RECEIVER_PARTY")?></span>
						<?php
						$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
						$accountTable->load(array('id' => $chalanItem['receiver_party']));
						?>
						<span><?php echo " : " . $accountTable->title;?></span>
					</td>
				</tr>
			</table>
			<table style="border-collapse:collapse;border:1px solid black;text-align:center;width:100%">
				<tr>
					<td rowspan="2" style="border:1px solid black;width:25%;">Description</td>
					<td colspan="2" style="border:1px solid black;width:25%;">No. Of Boxes</td>
					<td rowspan="2" style="border:1px solid black;width:15%;">Weight</td>
					<td rowspan="2" style="border:1px solid black;width:15%;">Rate</td>
					<td rowspan="2" style="border:1px solid black;width:20%;">Freight</td>
				</tr>
				<tr>
					<td style="border:1px solid black;">Kgs.</td>
					<td style="border:1px solid black;">Qty</td>
				</tr>
				<tr>
					<td style="border:1px solid black;"><?php echo Text::_("Trade mark : ") . $chalanItem['trade_mark'];?></td>
					<td style="border:1px solid black;"><?php echo $chalanItem['weight'];?></td>
					<td style="border:1px solid black;"><?php echo $chalanItem['units'];?></td>
					<td style="border:1px solid black;"><?php echo $chalanItem['weight'];?></td>
					<td style="border:1px solid black;"><?php echo $chalanItem['freight'];?></td>
					<td style="border:1px solid black;"><?php echo ($chalanItem['freight'] * $chalanItem['units']);?></td>
				</tr>
				<tr>
					<td style="border:1px solid black;"></td>
					<td style="border:1px solid black;"></td>
					<td style="border:1px solid black;"></td>
					<td style="border:1px solid black;"></td>
					<td style="border:1px solid black;"></td>
					<td style="border:1px solid black;"></td>
				</tr>
				<tr>
					<td colspan="4" rowspan="4" style="border:1px solid black;"><?php echo Text::_("COM_TMS_BILLT_NOTE");?></td>
					<td style="border:1px solid black;"><?php echo Text::_("Inam");?></td>
					<td style="border:1px solid black;"><?php echo ($chalanItem['inam'] * $chalanItem['units'])?></td>
				</tr>
				<tr>
					<?php
					$total = ($chalanItem['inam'] * $chalanItem['units']) + ($chalanItem['freight'] * $chalanItem['units']);
					?>
					<td style="border:1px solid black;"><?php echo Text::_("Total");?></td>
					<td style="border:1px solid black;"><?php echo ($chalanItem['inam'] * $chalanItem['units']) + ($chalanItem['freight'] * $chalanItem['units'])?></td>
				</tr>
				<tr>
					<td style="border:1px solid black;"><?php echo Text::_("Advance");?></td>
					<td style="border:1px solid black;"><?php echo $chalanItem['billt_paid'];?></td>
				</tr>
				<tr>
					<td style="border:1px solid black;"><?php echo Text::_("Balance");?></td>
					<td style="border:1px solid black;"><?php echo ($total - $chalanItem['billt_paid']);?></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td colspan="3">Driver's Sign</td>
					<td colspan="3">Authorized Sign</td>
				</tr>
			</table>
		</div>
	</div>
	<?php
}
