<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * TMS Account Table class
 *
 * @since  1.0.0
 */
class TmsTableTransaction extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__transport_transaction', 'id', $db);
	}

	/**
	 * Check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check
	 * @since   1.5
	 */
	public function check()
	{
		// Check for valid amount
		if (!empty($this->credit) && !empty($this->debit))
		{
			$this->setError(Text::_('COM_TMS_WARNING_CHECK_TRANSACTION_AMOUNT'));

			return false;
		}

		// Check for valid amount
		if (empty($this->credit) && empty($this->debit))
		{
			$this->setError(Text::_('COM_TMS_WARNING_EMPTY_TRANSACTION_AMOUNT'));

			return false;
		}

		// Check for valid amount
		if (!empty($this->credit))
		{
			if ($this->credit <= 0)
			{
				$this->setError(Text::_('COM_TMS_WARNING_INVALID_TRANSACTION_AMOUNT'));

				return false;
			}
		}

		// Check for valid amount
		if (!empty($this->debit))
		{
			if ($this->debit <= 0)
			{
				$this->setError(Text::_('COM_TMS_WARNING_INVALID_TRANSACTION_AMOUNT'));

				return false;
			}
		}

		$db = Factory::getDbo();

		// Check if transaction is done against valid account
		$table = Table::getInstance('Account', 'TmsTable', array('dbo', $db));
		$table->load(array('id' => $this->account_id));

		if (empty($table->id))
		{
			$this->setError(Text::_('COM_TMS_WARNING_TRANSACTION_AGAINST_INVALID_ACCOUNT'));

			return false;
		}

		return parent::check();
	}

	/**
	 * Stores account details.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		$date   = Factory::getDate()->toSql();
		$userId = Factory::getUser()->id;

		$this->modified = $date;

		if ($this->id)
		{
			// Existing item
			$this->modified_by = $userId;
		}
		else
		{
			// New item
			if (!(int) $this->created)
			{
				$this->created = $date;
			}

			if (empty($this->created_by))
			{
				$this->created_by = $userId;
			}
		}

		return parent::store($updateNulls);
	}
}
