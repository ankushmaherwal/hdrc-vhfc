<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

if (empty($displayData))
{
	return false;
}

$statementData = $displayData;
$params = ComponentHelper::getParams('com_tms');

$db = Factory::getDbo();
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');
$accountTable = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
?>
<div style="padding:15px;border-style:ridge;" class="statement-print">
	<div>
		<table>
			<tr style="text-align:center;">
				<td style="width:15%;">
					<h3 style="border-style:solid;border-radius:20px;"><?php echo Text::_($params->get('ch_company_name_short', 'XXX'));?></h3>
				</td>
				<td colspan="4">
					<h2><?php echo Text::_($params->get('ch_company_name', 'XXX'));?></h2>
					<h5><?php echo Text::_($params->get('ch_company_address', 'XXX'));?></h5>
				</td>
			</tr>
			<tr style="text-align:left;font-size:13px;">
				<td>
					<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_TRANSACTION_STATEMENT_FOR");?></span>
				</td>
				<td>
					<span style="font-weight: bold;"><?php echo $statementData['accountDetails']->title;?></span>
				</td>
				<td>
					<span style="font-weight: bold;"><?php echo Text::_("COM_TMS_TRANSACTION_STATEMENT_ACCOUNT_BALANCE");?></span>
				</td>
				<td>
					<?php $balanceSufix = ($statementData['accountBalance'] >= 0) ? ' Cr.' : ' Dr.';?>
					<span style="font-weight: bold;"><?php echo 'Rs ' . $statementData['accountBalance'] . $balanceSufix;?></span>
				</td>
			</tr>
			<?php 
			if (!empty($statementData['statementFrom']) || !empty($statementData['statementTo']))
			{
			?>
			<tr>
				<td colspan="4">
					<span style="font-weight:bold;font-size:13px">
						<?php 
							echo Text::_("COM_TMS_TRANSACTION_STATEMENT_PERIOD");

							if (empty($statementData['statementFrom']))
							{
								$statementData['statementFrom'] = Factory::getDate();
							}

							if (empty($statementData['statementTo']))
							{
								$statementData['statementTo'] = Factory::getDate();
							}

							echo Factory::getDate($statementData['statementFrom'])->format(Text::_("COM_TMS_DISPLAY_DATE_FORMAT")) . ' - ';
							echo Factory::getDate($statementData['statementTo'])->format(Text::_("COM_TMS_DISPLAY_DATE_FORMAT"));
						?>
					</span>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<table style="border-collapse:collapse;border:1px solid black;text-align:center;width:100%;font-size:12px;">
			<tr>
				<td style="border:1px solid black;width:10%;">Date</td>
				<td style="border:1px solid black;width:40%;">Description</td>
				<td style="border:1px solid black;width:10%;">Post Ref.</td>
				<td style="border:1px solid black;width:20%;">Debit</td>
				<td style="border:1px solid black;width:20%;">Credit</td>
			</tr>
			<?php
			foreach ($statementData['transactions'] as $transaction)
			{
				?>
				<tr>
					<td style="border:1px solid black;width:10%;"><?php echo Factory::getDate($transaction->date)->format("d-m-y");?></td>
					<td style="border:1px solid black;width:40%;text-align:left;"><?php echo $transaction->description?></td>
					<td style="border:1px solid black;width:10%;"><?php echo $transaction->id?></td>
					<?php
					foreach ($transaction->details as $detail)
					{
						if ($detail->account_id == $statementData['accountDetails']->id && !empty($detail->debit))
						{
							?>
								<td style="border:1px solid black;width:20%;"><?php echo $detail->debit;?></td>
							<?php
						}

						if ($detail->account_id == $statementData['accountDetails']->id && !empty($detail->credit))
						{
							?>
								<td style="border:1px solid black;width:20%;"><?php echo $detail->credit;?></td>
							<?php
						}
					}
					?>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
</div>