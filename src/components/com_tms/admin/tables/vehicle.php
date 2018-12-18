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
 * TMS Vehicle Table class
 *
 * @since  1.0.0
 */
class TmsTableVehicle extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__transport_vehicle', 'id', $db);
	}

	/**
	 * Stores vehicle details.
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
		$this->registration_number = strtoupper($this->registration_number);

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
	 * Check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check
	 * @since   1.5
	 */
	public function check()
	{
		// Check if vehicle already registered
		$db = Factory::getDbo();
		$table = Table::getInstance('Vehicle', 'TmsTable', array('dbo', $db));
		$table->load(array('registration_number' => $this->registration_number));

		// Check for valid name
		if (trim($table->registration_number) != '')
		{
			$this->setError(Text::_('COM_TMS_VEHICLE_ALREADY_REGISTERED'));

			return false;
		}

		return parent::check();
	}
}
