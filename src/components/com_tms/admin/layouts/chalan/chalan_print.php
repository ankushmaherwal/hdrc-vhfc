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

if (empty($displayData))
{
	return false;
}

$params = ComponentHelper::getParams('com_tms');
$formData = $displayData->getData();
$db = Factory::getDbo();
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');
$vehicleTable = Table::getInstance('Vehicle', 'TmsTable', array('dbo', $db));
$vehicleTable->load(array('id' => $formData->get('vehicle_id')));
$vehicle_number = $vehicleTable->registration_number;

if (!empty($formData))
{
	$chalanItems = $formData->get('chalan_items');
	?>
	<div style="padding:15px;border-style:ridge;">
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
				<tr>
					<td style="width:15%;">
						<h1 style="text-align:center;border-style:solid;border-radius:20px;"><?php echo Text::_($params->get('ch_company_name_short', 'XXX'));?></h1>
					</td>
					<td colspan="4">
						<h1 style="text-align:center"><?php echo Text::_($params->get('ch_company_name', 'XXX'));?></h1>
					</td>
				</tr>
			</table>
		</div>
		<div>
			<table>
				<tr>
					<td style="width:10%;text-align:left;">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_SNO")?></span>
						<span><?php echo sprintf('%06d', $formData->get('id'));?></span>
					</td>
					<td style="width:10%;text-align:center;">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_TRUCK_NUMBER")?></span>
						<span><?php echo $vehicle_number;?></span>
					</td>
					<td style="width:10%;text-align:right;">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_DATE")?></span>
						<span><?php echo HTMLHelper::date($formData->get('date'), Text::_('COM_TMS_DISPLAY_DATE_FORMAT'));?></span>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_PARTY")?></span>
						<span><?php echo $formData->get('party_name');?></span>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_DELIVERY_NOTE")?></span>
					</td>
				</tr>
			</table>
			<table style="border-collapse:collapse;border:1px solid black;">
				<tr>
					<th style="width:5%;border:1px solid black;">GM.No.</th>
					<th style="width:15%;border:1px solid black;">consignor's Name</th>
					<th style="width:15%;border:1px solid black;">Tread mark</th>
					<th style="width:10%;border:1px solid black;">Weight</th>
					<th style="width:10%;border:1px solid black;">No. of Boxes</th>
					<th style="width:10%;border:1px solid black;">Freight</th>
					<th style="width:10%;border:1px solid black;">Inam</th>
					<th style="width:15%;border:1px solid black;">Shop No.</th>
					<th style="width:10%;border:1px solid black;">Remarks</th>
				</tr>
				<?php
				$totalUnits = 0;
				$totalFreight = 0;
				$totalInam = 0;

				foreach ($chalanItems as $k => $chalanItem)
				{
					?>
					<tr style="text-align:center;">
						<td style="width:5%;border:1px solid black;"><?php echo $k+1;?></td>
						<?php
							$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
							$accountTable->load(array('id' => $chalanItem['sender_party']));
							$senderParty = $accountTable->title;
						?>
						<td style="width:15%;border:1px solid black;"><?php echo $senderParty;?></td>
						<td style="width:15%;border:1px solid black;"><?php echo $chalanItem['sender'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['weight'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['units'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['freight']*$chalanItem['units'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['inam']*$chalanItem['units'];?></td>
						<td style="width:15%;border:1px solid black;"><?php echo $chalanItem['receiver'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['remarks'];?></td>
					</tr>
					<?php
					$totalUnits = $totalUnits + $chalanItem['units'];
					$totalFreight = $totalFreight + ($chalanItem['freight']*$chalanItem['units']);
					$totalInam = $totalInam + ($chalanItem['inam']*$chalanItem['units']);
				}
				?>
				<tr>
					<td colspan="4" style="border:1px solid black;text-align:center;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_TOTAL");?></td>
					<td style="width:10%;border:1px solid black;"><?php echo $totalUnits;?></td>
					<td style="width:10%;border:1px solid black;"><?php echo $totalFreight;?></td>
					<td style="width:15%;border:1px solid black;"><?php echo $totalInam;?></td>
					<td colspan="2" style="width:15%;border:1px solid black;"></td>
				</tr>
			</table>
			<br />
			<br />
			<div>
			<?php echo Text::_("COM_TMS_PRINT_CHALAN_DRIVERS_UNDERTAKING");?>
			</div>
			<br />
			<br />
			<div>
				<table>
					<tr>
						<th style="text-align:left;width:30%;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_DRIVERS_SIGNATURE");?></th>
						<th style="text-align:left;width:30%;"><?php echo '';?></th>
						<th style="text-align:right;width:40%;"><?php echo Text::sprintf("COM_TMS_PRINT_CHALAN_OWNERS_SIGNATURE", $params->get('ch_company_name', 'XXX'));?></th>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php
}
