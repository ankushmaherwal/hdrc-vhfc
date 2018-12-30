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
?>
<style>
.chalan-print tr {
	height:30px !important;
}
</style>
<?php
if (!empty($formData))
{
	$chalanItems = $formData->get('chalan_items');
	$thirdPartyPaid = $formData->get('third_party_paid');
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
				<br />
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
					<th style="width:15%;border:1px solid black;">Shop No.</th>
					<th style="width:10%;border:1px solid black;">Weight</th>
					<th style="width:10%;border:1px solid black;">No. of Boxes</th>
					<th style="width:10%;border:1px solid black;">Inam</th>
					<th style="width:10%;border:1px solid black;">Freight</th>
				</tr>
				<?php
				$totalUnits   = 0;
				$totalFreight = 0;
				$totalInam    = 0;
				$paidInParty  = 0;

				for ($i = 0; $i < 15; $i++)
				{
					if (!isset($chalanItems[$i+1]) && $i<15)
					{
						$chalanItems[$i+1] = array('id' => '', 'chalan_id' => '', 'sender_party' => '', 'receiver_party' => '', 'trade_mark' => '', 'units' => '', 'weight' => '', 'freight' => '', 'inam' => '', 'billt_paid_id' => '', 'billt_paid' => '');
					}

					$chalanItem = $chalanItems[$i];

					?>
					<tr style="text-align:center;">
						<td style="width:5%;border:1px solid black;"><?php echo $i+1;?></td>
						<?php
							$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
							$accountTable->load(array('id' => $chalanItem['sender_party']));
							$senderParty = $accountTable->title;
							$accountTable->load(array('id' => $chalanItem['receiver_party']));
							$receiverParty = $accountTable->title;
							$freight = ($chalanItem['freight']*$chalanItem['units']);

							if (isset($chalanItem['billt_paid']))
							{
								$freight -= $chalanItem['billt_paid'];
							}

							// Update flag if there is party paid entry in chalan
							if (isset($chalanItem['billt_paid']) && $paidInParty != 1)
							{
								$paidInParty = 1;
							}

							$inam = $chalanItem['inam'] * $chalanItem['units'];

							$inam = empty($inam) ? '' : $inam;
							$freight = empty($freight) ? '' : $freight;
						?>
						<td style="width:15%;border:1px solid black;"><?php echo $senderParty;?></td>
						<td style="width:15%;border:1px solid black;"><?php echo $chalanItem['trade_mark'];?></td>
						<td style="width:15%;border:1px solid black;"><?php echo $receiverParty;?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['weight'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $chalanItem['units'];?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $inam;?></td>
						<td style="width:10%;border:1px solid black;"><?php echo $freight;?></td>
					</tr>
					<?php
					$totalUnits   = $totalUnits + $chalanItem['units'];
					$totalFreight = $totalFreight + $freight;
					$totalInam    = $totalInam + ($chalanItem['inam']*$chalanItem['units']);
				}

				$totalAdvance = $formData->get('advance', 0, 'INT');
				?>
				<tr style="text-align:center;">
					<td colspan="5" style="border:1px solid black;text-align:center;"><?php echo Text::_("COM_TMS_PRINT_CHALAN_TOTAL");?></td>
					<td style="width:10%;border:1px solid black;"><?php echo $totalUnits;?></td>
					<td style="width:15%;border:1px solid black;"><?php echo $totalInam;?></td>
					<td style="width:10%;border:1px solid black;"><?php echo $totalFreight;?></td>
				</tr>
				<tr style="text-align:center;">
					<td colspan="5" style="width:10%;border:0px;"></td>
					<td colspan="2" style="width:10%;border:1px solid black;"><?php echo Text::_("Advance");?></td>
					<td style="width:10%;border:1px solid black;"><?php echo $totalAdvance;?></td>
				</tr>
				<tr style="text-align:center;">
					<td colspan="5" style="width:10%;border:0px;"></td>
					<td colspan="2" style="width:10%;border:1px solid black;"><?php echo Text::_("Total");?></td>
					<td style="width:10%;border:1px solid black;"><?php echo ($totalFreight + $totalAdvance);?></td>
				</tr>
			</table>
			<br />
			<?php
				if (!empty($thirdPartyPaid))
				{
					echo Text::_("COM_TMS_PRINT_CHALAN_NOTE");

					foreach ($thirdPartyPaid as $paid)
					{
						$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
						$accountTable->load(array('id' => $paid['third_party_paid']));

						echo Text::sprintf("COM_TMS_PRINT_CHALAN_PAID_NOTE", $paid['chalan_billt_paid'], $accountTable->title);
					}
				}
				else
				{
					echo "<br /><br />";
				}
			?>
			<div>
			<?php echo Text::_("COM_TMS_PRINT_CHALAN_DRIVERS_UNDERTAKING");?>
			</div>
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
	// Print bill-t for chalan items with paid entry
	if ($paidInParty)
	{
		?>
		<div id="printChalanBilltContent" class="d-none">
			<?php
			$count = 0;
			foreach ($chalanItems as $chalanItem)
			{
				if (isset($chalanItem['billt_paid']) && !empty($chalanItem['billt_paid']))
				{
					$count++;
					$style = '';

					if ($count%2 == 0)
					{
						$style = "page-break-after:always;";
					}
					?>
					<div style="<?php echo $style;?>">
						<?php
						$layout = new JLayoutFile('billt_print', $basePath = JPATH_SITE . '/administrator/components/com_tms/layouts/chalan');
						echo $layout->render($chalanItem);
						?>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
	}
}
