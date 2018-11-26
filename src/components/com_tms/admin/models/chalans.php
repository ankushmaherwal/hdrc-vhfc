<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;

/**
 * TMS Chalans Model
 *
 * @since  1.0.0
 */
class TmsModelChalans extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'party_name',
				'destination'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list datt.
	 *
	 * @return  string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('c.*');
		$query->from($db->quoteName('#__transport_chalan', 'c'));

		// Join over the categories.
		$query->select($db->quoteName('v.registration_number', 'registration_number'));
		$query->join('LEFT', $db->quoteName('#__transport_vehicle', 'v') . ' ON ' . $db->quoteName('v.id') . '=' . $db->quoteName('c.vehicle_id'));

		// Filter: search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$query->where($db->quoteName('c.id') . ' = ' . (int) $search);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', $db->quoteName('c.id'));
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
