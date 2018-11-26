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
 * TMS Transactions Model
 *
 * @since  1.0.0
 */
class TmsModelTransactions extends ListModel
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
				'account_id',
				'category_id',
				'type',
				'date',
				'published'
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
		$query->select('t.*');
		$query->from($db->quoteName('#__transport_transaction', 't'));

		// Join over the categories.
		$query->select($db->quoteName('ac.title', 'account_title'));
		$query->join('LEFT', $db->quoteName('#__transport_account', 'ac') . ' ON ' . $db->quoteName('ac.id') . '=' . $db->quoteName('t.account_id'));

		$query->where($db->quoteName('ac.published') . '=1');

		// Filter: search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$query->where($db->quoteName('t.id') . ' = ' . (int) $search);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('t.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where($db->quoteName('t.published') . ' IN (0, 1)');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', $db->quoteName('t.id'));
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
