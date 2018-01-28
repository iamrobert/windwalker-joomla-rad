<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\View\Html;

use Joomla\CMS\Form\Form;
use Windwalker\DI\Container;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Model\Model;

/**
 * The edit view.
 *
 * @since 2.0
 */
class EditView extends ItemHtmlView
{
	/**
	 * Method to instantiate the view.
	 *
	 * @param Model             $model     The model object.
	 * @param Container         $container DI Container.
	 * @param array             $config    View config.
	 * @param \SplPriorityQueue $paths     Paths queue.
	 */
	public function __construct(Model $model = null, Container $container = null, $config = array(), \SplPriorityQueue $paths = null)
	{
		parent::__construct($model, $container, $config, $paths);

		if (!$this->toolbarConfig)
		{
			$this->toolbarConfig = ArrayHelper::getValue($config, 'toolbar', array());
		}
	}

	/**
	 * Prepare render hook.
	 *
	 * @return  void
	 */
	protected function prepareRender()
	{
		parent::prepareRender();

		$this['form'] = $this['form'] ? : $this->get('Form');

		if ($errors = $this['state']->get('errors'))
		{
			$this->addMessage($errors);
		}

		// Configure UI
		$this->addToolbar();
		$this->setTitle();

		$input = $this->getContainer()->get('input');

		$input->set('hidemainmenu', true);
	}

	/**
	 * Set title of this page.
	 *
	 * @param string $title Page title.
	 * @param string $icons Title icon.
	 *
	 * @return  void
	 */
	protected function setTitle($title = null, $icons = 'pencil-2 article')
	{
		if (!$title)
		{
			$title = \Joomla\CMS\Language\Text::_(sprintf('COM_%s_%s_TITLE_ITEM_EDIT', strtoupper($this->prefix), strtoupper($this->viewItem)));
		}

		parent::setTitle($title, $icons);
	}

	/**
	 * Configure the toolbar button set.
	 *
	 * @param   array   $buttonSet Customize button set.
	 * @param   object  $canDo     Access object.
	 *
	 * @return  array
	 */
	protected function configureToolbar($buttonSet = array(), $canDo = null)
	{
		return array(
			'apply' => array(
				'handler'  => 'apply',
				'args'     => array($this->viewItem . '.edit.apply'),
				'access'   => true,
				'priority' => 1000
			),

			'save' => array(
				'handler'  => 'save',
				'args'     => array($this->viewItem . '.edit.save'),
				'access'   => true,
				'priority' => 900
			),

			'save2new' => array(
				'handler'  => 'save2new',
				'args'     => array($this->viewItem . '.edit.save2new'),
				'access'   => true,
				'priority' => 800
			),

			'save2copy' => array(
				'handler'  => 'save2copy',
				'args'     => array($this->viewItem . '.edit.save2copy'),
				'access'   => true,
				'priority' => 700
			),

			'cancel' => array(
				'handler'  => 'cancel',
				'args'     => array($this->viewItem . '.edit.cancel'),
				'access'   => true,
				'priority' => 600
			),
		);
	}

	/**
	 * Get form method to support 3.7 fields.
	 *
	 * @return  Form
	 */
	public function getForm()
	{
		if (!$this->data->form instanceof Form)
		{
			$this->data->form = $this->get('Form');
		}

		return $this->data->form;
	}
}
