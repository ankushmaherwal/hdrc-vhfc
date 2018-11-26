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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * TMS - Freight Model
 *
 * @since  1.0.0
 */
class TmsModelFreight extends BaseDatabaseModel
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
	public function getTable($type = 'Freight', $prefix = 'TmsTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get freight data.
	 *
	 * @return  ARRAY  freight data
	 *
	 * @since   1.0.0
	 */
	public function getItem()
	{
		// Get destinations from config
		$params = ComponentHelper::getParams('com_tms');
		$destinationsList = $params->get('destinations_list', '', 'STRING');
		$destinationsList = explode(',', $destinationsList);

		foreach ($destinationsList as &$destination)
		{
			$destination = trim($destination);
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__transport_freight');
		$db->setQuery($query);

		$freightData = $db->loadAssocList();

		$retun = array();

		for ($i = 0; $i < count($destinationsList); $i++)
		{
			$rateSet = 0;

			for ($j = 0; $j < count($freightData); $j++)
			{
				if ($destinationsList[$i] == $freightData[$j]['destination'])
				{
					$retun[$destinationsList[$i]] = $freightData[$j];
					$rateSet = 1;

					break;
				}
			}

			if (empty($rateSet))
			{
				$retun[$destinationsList[$i]] = array("destination" => $destinationsList[$i], "box_weight" => '0');
			}
		}

		return $retun;
	}

	public function save($freights)
	{
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');
		$db = Factory::getDbo();

		foreach ($freights as $destination => $freight)
		{
			$freightTable = JTable::getInstance('Freight', 'TmsTable', array('dbo', $db));
			$freightTable->load(array('destination' => $destination));
			$freightTable->destination = $destination;
			$freightTable->box_weight  = json_encode($freight);

			if (!$freightTable->store())
			{
				return false;
			}
		}

		return true;
	}
}
