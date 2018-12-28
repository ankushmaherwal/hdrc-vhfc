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
		$db = Factory::getDbo();

		foreach ($freights as $destination => $freight)
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('destination', 'box_weight')));
			$query->from($db->quoteName('#__transport_freight'));
			$query->where($db->quoteName('destination') . ' = ' . $db->quote($destination));
			$db->setQuery($query);
			$data = $db->loadObject();

			if (isset($data->destination))
			{
				$data->destination = $destination;
				$data->box_weight = json_encode($freight);

				$result = $db->updateObject('#__transport_freight', $data, 'destination');
			}
			else
			{
				$data->destination = $destination;
				$data->box_weight = json_encode($freight);
				$result = $db->insertObject('#__transport_freight', $data, 'destination');
			}

			if (empty($result))
			{
				return false;
			}
		}

		return true;
	}
}
