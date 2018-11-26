<?php
/**
 * @package    TMS
 * @author     Ankushkumar Maherwal <ankush.maherwal@gmail.com>
 * @copyright  Copyright (c) 2018-2018 Ankushkumar Maherwal. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

/**
 * TMS Vehicle Controller
 *
 * @since  1.0.0
 */
class TmsControllerVehicle extends FormController
{
	/**
	 * Implement to allowAdd or not
	 *
	 * @return bool
	 */
	protected function allowAdd($data = Array())
	{
		return Factory::getUser()->authorise("core.create", "com_tms");
	}

	/**
	 * Implement to allow edit or not
	 *
	 * @return bool
	 */
	protected function allowEdit($data = Array(), $key = 'id')
	{
		return Factory::getUser()->authorise("core.edit", "com_tms");
	}
}
