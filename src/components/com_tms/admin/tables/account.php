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
class TmsTableAccount extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__transport_account', 'id', $db);
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
		// Check for valid name
		if (trim($this->title) == '')
		{
			$this->setError(Text::_('COM_TMS_ACCOUNT_INVALID_ACCOUNT_NAME'));

			return false;
		}

		// Generate a valid alias
		$this->generateAlias();

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

	/**
	 * Generate a valid alias from title / date.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @return  string
	 */
	public function generateAlias()
	{
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		// Check if account with same alias is present
		$table = Table::getInstance('Account', 'TmsTable', array('dbo', $db));

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$msg = Text::_('COM_TMS_SAVE_ALIAS_WARNING');

			while ($table->load(array('alias' => $this->alias)))
			{
				$this->alias = JString::increment($this->alias, 'dash');
			}

			Factory::getApplication()->enqueueMessage($msg, 'warning');
		}

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return $this->alias;
	}
}
