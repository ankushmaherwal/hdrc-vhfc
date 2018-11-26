<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * TMS - Chalan Model
 *
 * @since  1.0.0
 */
class TmsModelChalan extends AdminModel
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Chalan', $prefix = 'TmsTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_tms.chalan',
			'chalan',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_tms.edit.chalan.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
			$data->chalan_items = $this->getChalanItem($data->id);
		}

		return $data;
	}

	/**
	 * Function to save chalan
	 *
	 * @param   ARRAY  $data  chalan data
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function save($data)
	{
		// Load tms tables
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');

		$valid = $this->validateChalanItems($data['chalan_items']);

		if (!$valid)
		{
			return false;
		}

		if (parent::save($data))
		{
			$chalanId = (int) $this->getState($this->getName() . '.id');
			$result = $this->saveChalanItem($data['chalan_items'], $chalanId);

			if (!$result)
			{
				$this->setError(JText::_('COM_TMS_CHALAN_CHALAN_ITEM_SAVE_ERROR'));

				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Function to get chalan items
	 *
	 * @param   ARRAY  $data      chalan item data
	 * @param   INT    $chalanId  chalan id
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function saveChalanItem($data, $chalanId)
	{
		foreach ($data as $chalanItem)
		{
			// If empty chalan item found then delete the chalan item
			if (empty($chalanItem['sender']) && empty($chalanItem['receiver']) && empty($chalanItem['units']) && empty($chalanItem['freight']) && empty($chalanItem['inam']) && empty($chalanItem['weight']) && empty($chalanItem['sender_party']) && !empty($chalanItem['id']))
			{
				$chalanItemTable = Table::getInstance('ChalanItem', 'TmsTable', array());
				$chalanItemTable->load(array('id' => $chalanItem['id']));
				$chalanItemTable->delete();
			}

			// If sender or receiver or units or freight or weight is empty then don't save the record
			if (empty($chalanItem['sender']) || empty($chalanItem['receiver']) || empty($chalanItem['units']) || empty($chalanItem['freight']) || empty($chalanItem['inam']) || empty($chalanItem['sender_party']) || empty($chalanItem['weight']))
			{
				continue;
			}

			$chalanItemTable = Table::getInstance('ChalanItem', 'TmsTable', array());

			$chalanItemTable->id = !empty($chalanItem['id']) ? $chalanItem['id'] : '';
			$chalanItemTable->chalan_id = (int) $chalanId;
			$chalanItemTable->sender_party = $chalanItem['sender_party'];
			$chalanItemTable->sender = $chalanItem['sender'];
			$chalanItemTable->receiver = $chalanItem['receiver'];
			$chalanItemTable->units = (int) $chalanItem['units'];
			$chalanItemTable->weight = (int) $chalanItem['weight'];
			$chalanItemTable->freight = (int) $chalanItem['freight'];
			$chalanItemTable->inam = (int) $chalanItem['inam'];
			$chalanItemTable->remarks = $chalanItem['remarks'];

			// Add entry in chalan item table
			if (!$chalanItemTable->store())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Function to get chalan items
	 *
	 * @param   INT  $chalanId  chalan id
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function getChalanItem($chalanId)
	{
		if (empty($chalanId))
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__transport_chalan_item');
		$query->where('chalan_id' . ' = ' . (int) $chalanId);
		$db->setQuery($query);
		$chalanItems = $db->loadAssocList();

		return $chalanItems;
	}

	/**
	 * Function to validate chalan items
	 *
	 * @param   ARRAY  $items  Array of chalan items
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function validateChalanItems($items)
	{
		// Count of valid entries
		$ValidEntries = 0;

		foreach ($items as $item)
		{
			if (!empty($item['sender']) || !empty($item['receiver']) || !empty($item['weight']) || !empty($item['units']) || !empty($item['freight']) || !empty($item['inam']) || !empty($item['sender_party']))
			{
				if (empty($item['sender']) || empty($item['receiver']) || empty($item['units']) || empty($item['freight']) || empty($item['inam']) || empty($item['weight']) || empty($item['sender_party']))
				{
					$this->setError(JText::_('COM_TMS_CHALAN_CHALAN_ITEMS_SAVE_ERROR'));

					return false;
				}
				else
				{
					$ValidEntries++;
				}
			}
		}

		if (count($ValidEntries))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Delete chalan and chalan items
	 *
	 * @param   object  &$pks  The primary key related to the chalan that was deleted.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function delete(&$pks)
	{
		$return = parent::delete($pks);

		if ($return)
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__transport_chalan_item'));
			$query->where($db->quoteName('chalan_id') . ' IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return $return;
	}
}
