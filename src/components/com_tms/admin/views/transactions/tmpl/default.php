<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
?>
<form action="index.php?option=com_tms&view=transactions" method="post" id="adminForm" name="adminForm">
	<div id="tms-wrapper">
		<div id="j-sidebar-container" class="span2">
			<?php echo JHtmlSidebar::render(); ?>
		</div>
		<div id="j-main-container" class="span10">
			<div class="row-fluid">
				<div class="span12">
					<?php
						echo LayoutHelper::render(
							'joomla.searchtools.default',
							array('view' => $this)
						);
					?>
				</div>
			</div>
			<div>&nbsp;</div>
			<?php
			if (!empty($this->items))
			{
			?>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th width="2%">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="15%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_ACCOUNTS_TITLE', 'account_title', $listDirn, $listOrder); ?>
						</th>
						<th width="5%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_STATE', 'published', $listDirn, $listOrder); ?>
						</th>
						<th width="15%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_DATE', 'date', $listDirn, $listOrder); ?>
						</th>
						<th width="15%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_DEBIT', 'debit', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_CREDIT', 'credit', $listDirn, $listOrder); ?>
						</th>
						<th width="30%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_DESC', 'description', $listDirn, $listOrder); ?>
						</th>
						<th width="8%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_TMS_TRANSACTIONS_ID', 'id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($this->items as $i => $row)
				{
					//~ echo "<pre>";print_r($row);die;
					$link = 'index.php?option=com_tms&task=transaction.edit&id=' . $row->id;
					?>
					<tr>
						<td>
							<?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo Text::_('COM_TMS_EDIT_TRANSACTION'); ?>">
								<?php echo $row->account_title; ?>
							</a>
						</td>
						<td align="center">
							<?php echo HTMLHelper::_('jgrid.published', $row->published, $i, 'transactions.', true, 'cb'); ?>
						</td>
						<td align="center">
							<?php echo HTMLHelper::date($row->date, Text::_('COM_TMS_DISPLAY_DATE_FORMAT')); ?>
						</td>
						<td align="center">
							<?php echo empty($row->debit) ? '-' : $row->debit; ?>
						</td>
						<td align="center">
							<?php echo empty($row->credit) ? '-' : $row->credit; ?>
						</td>
						<td align="center">
							<?php echo $row->description; ?>
						</td>
						<td>
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php
			}
			else
			{
				?>
				<div class="alert alert-info"><?php echo Text::_("COM_TMS_NO_RECORDS_FOUND");?></div>
				<?php
			}
			?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</div>
</form>
