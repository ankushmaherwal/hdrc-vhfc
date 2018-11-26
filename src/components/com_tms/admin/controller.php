<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

/**
 * General Controller of TMS component
 *
 * @package     TMS
 * @subpackage  com_tms
 * @since       1.0.0
 */
class TmsController extends BaseController
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $default_view = 'accounts';

	/**
	 * Function to get freight as per destination
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public function getFreight()
	{
		$app   = Factory::getApplication();
		$input = $app->input;

		$destination = $input->get('destination', '', 'STRING');

		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tms/tables');

		$freightTable = Table::getInstance('Freight', 'TmsTable');
		$freightTable->load(array('destination' => $destination));

		$data = $freightTable->getProperties();

		if (!empty($data))
		{
			echo json_encode($data);
		}

		jexit();
	}
}
