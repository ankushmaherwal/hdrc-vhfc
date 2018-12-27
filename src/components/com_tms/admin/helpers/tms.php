<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * TMS component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since  1.0.0
 */
abstract class TmsHelper extends ContentHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $submenu  submenu
	 *
	 * @return Bool
	 */
	public static function addSubmenu($submenu)
	{
		$app       = Factory::getApplication();
		$extension = $app->input->get('extension', '', "STRING");

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_ACCOUNTS'),
			'index.php?option=com_tms&view=accounts', (($submenu === 'accounts') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_VEHICLES'),
			'index.php?option=com_tms&view=vehicles', (($submenu === 'vehicles') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_ACCOUNT_CATEGORY'),
			'index.php?option=com_categories&view=categories&extension=com_tms.account', ($submenu == 'categories.account') ? true : false
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_TRANSACTIONS'),
			'index.php?option=com_tms&view=transactions', (($submenu === 'transactions') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_TRANSACTION_CATEGORY'),
			'index.php?option=com_categories&view=categories&extension=com_tms.transaction', ($submenu == 'categories.transaction') ? true : false
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_CHALANS'),
			'index.php?option=com_tms&view=chalans', (($submenu === 'chalans') ? true : false)
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_TMS_MANAGE_FREIGHT'),
			'index.php?option=com_tms&view=freight', (($submenu === 'freight') ? true : false)
		);
	}
}
