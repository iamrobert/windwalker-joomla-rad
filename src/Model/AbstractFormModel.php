<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Model;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\PluginHelper;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Model\Exception\ValidateFailException;

/**
 * Prototype form model.
 *
 * @see   JForm
 * @see   JFormField
 * @see   JFormRule
 * @since 2.0
 */
abstract class AbstractFormModel extends ItemModel
{
	/**
	 * Array of form objects.
	 *
	 * @var  array
	 */
	protected $forms = array();

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$config = array(
			'control'   => 'jform',
			'load_data' => $loadData
		);

		return $this->loadForm($this->option . '.' . $this->getName(), $this->getName(), $config);
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string    $name    The name of the form.
	 * @param   string    $source  The form source. Can be XML string if file flag is set to false.
	 * @param   array     $options Optional array of options for the form creation.
	 * @param   boolean   $clear   Optional argument to force load a new form.
	 * @param   string    $xpath   An optional xpath to search for the fields.
	 *
	 * @throws \Exception
	 * @return  mixed  JForm object on success, False on error.
	 *
	 * @see     JForm
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = null)
	{
		// Handle the optional arguments.
		$options['control'] = ArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = sha1($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->forms[$hash]) && !$clear)
		{
			return $this->forms[$hash];
		}

		// Set Form paths
		static $formLoaded;

		if (!$formLoaded)
		{
			// Get the form.
			// Register the paths for the form
			$paths = new \SplPriorityQueue;
			$paths->insert(JPATH_COMPONENT . '/model/form', 'normal');
			$paths->insert(JPATH_COMPONENT . '/model/field', 'normal');
			$paths->insert(JPATH_COMPONENT . '/model/rule', 'normal');

			// Legacy support to be removed in 4.0.
			$paths->insert(JPATH_COMPONENT . '/models/forms', 'normal');
			$paths->insert(JPATH_COMPONENT . '/models/fields', 'normal');
			$paths->insert(JPATH_COMPONENT . '/models/rules', 'normal');

			Form::addFormPath(JPATH_COMPONENT . '/models/forms');
			Form::addFieldPath(JPATH_COMPONENT . '/models/fields');
			Form::addRulePath(JPATH_COMPONENT . '/models/rules');

			Form::addFormPath(JPATH_COMPONENT . '/model/form');
			Form::addFieldPath(JPATH_COMPONENT . '/model/field');
			Form::addRulePath(JPATH_COMPONENT . '/model/rule');

			// Set Form paths for Windwalker
			Form::addFormPath(JPATH_COMPONENT . '/model/form/' . strtolower($this->getName()));
			Form::addFieldPath(JPATH_COMPONENT . '/model/field/' . strtolower($this->getName()));
			Form::addRulePath(JPATH_COMPONENT . '/model/rule/' . strtolower($this->getName()));

			$formLoaded = true;
		}

		try
		{
			$form = Form::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		}
		catch (\Exception $e)
		{
			throw $e;
		}

		// Store the form for later.
		$this->forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	abstract protected function loadFormData();

	/**
	 * Method to allow derived classes to preprocess the data.
	 *
	 * @param   string  $context  The context identifier.
	 * @param   mixed   &$data    The data to be processed. It gets altered directly.
	 *
	 * @return  void
	 */
	protected function preprocessData($context, &$data)
	{
		// Get the dispatcher and load the users plugins.
		$dispatcher = $this->getContainer()->get('event.dispatcher');

		PluginHelper::importPlugin('content');

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array($context, $data));

		// Check for errors encountered while preparing the data.
		if (count($results) > 0 && in_array(false, $results, true))
		{
			$this->state->set('errors', array($dispatcher->getError()));
		}
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   Form    $form  A JForm object.
	 * @param   mixed   $data  The data expected for the form.
	 * @param   string  $group The name of the plugin group to import (defaults to "content").
	 *
	 * @throws  \Exception if there is an error in the form event.
	 * @return  void
	 *
	 * @see     JFormField
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		PluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = $this->getContainer()->get('event.dispatcher');

		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof \Exception))
			{
				throw new \Exception($error);
			}
		}
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   Form    $form  The form to validate against.
	 * @param   array   $data  The data to validate.
	 * @param   string  $group The name of the field group to validate.
	 *
	 * @throws  ValidateFailException
	 * @throws  \Exception
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 */
	public function validate($form, $data, $group = null)
	{
		// Filter and validate the form data.
		/** @var $form Form */
		$data   = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof \Exception)
		{
			throw $return;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			throw new ValidateFailException($form->getErrors());
		}

		return $data;
	}
}
