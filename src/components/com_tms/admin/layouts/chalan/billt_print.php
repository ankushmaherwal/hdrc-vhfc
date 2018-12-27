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
	<div style="padding:15px;border-style:ridge;page-break-after:always;" class="chalan-print">
		<div>
			<table>
				<tr>
					<td colspan="3" style="width:30%;text-align:left;">
						<h4><?php echo Text::sprintf("COM_TMS_PRINT_CHALAN_JURISDICTION", $params->get('ch_jurisdiction', 'XXX'));?></h4>
					</td>
					<td style="width:15%;text-align:center;">
						<h4><?php echo Text::_($params->get('ch_owners_name', 'XXX'));?></h4>
					</td>
					<td style="width:30%;text-align:right;">
						<h4><?php echo Text::sprintf("COM_TMS_PRINT_CHALAN_CONTACT_NUMBER", $params->get('ch_contact_number', 'XXX'));?></h4>
					</td>
				</tr>
				<tr style="text-align:center;">
					<td style="width:15%;">
						<h2 style="border-style:solid;border-radius:20px;"><?php echo Text::_($params->get('ch_company_name_short', 'XXX'));?></h2>
					</td>
					<td colspan="4">
						<h1><?php echo Text::_($params->get('ch_company_name', 'XXX'));?></h1>
						<h4><?php echo Text::_($params->get('ch_company_address', 'XXX'));?></h4>
					</td>
				</tr>
				<tr style="text-align:center;">
					<td>
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_SNO")?></span>
						<span><?php echo sprintf('%06d', $chalanItem['id']);?></span>
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
			<table>
				<tr>
					<td>
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_CHALAN_ITEM_SENDER_PARTY")?></span>
						<?php
						$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
						$accountTable->load(array('id' => $chalanItem['sender_party']));
						?>
						<span><?php echo " : " . $accountTable->title;?></span>
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
		</div>
	</div>
	<?php
}
