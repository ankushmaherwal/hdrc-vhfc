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
	 * Implement to allow edit or not
	 *
	 * @return bool(
	 */
	public function popupSave()
	{
		$return = array();
		$jInput = Factory::getApplication()->input->post;
		$model = $this->getModel();
		$data = $jInput->get('jform', array(), 'ARRAY');
		$form = $model->getForm($data);
		$data = $model->validate($form, $data);
		$status = $model->save($data);
		$vehicleId = (int) $model->getState($model->getName() . '.id');

		if (!empty($vehicleId))
		{
			$return['id'] = $vehicleId;
			$return['title'] = strtoupper($data['registration_number']);
		}

		echo new JResponseJson($return);
	}
}
