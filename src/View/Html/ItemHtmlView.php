<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\View\Html;

use Windwalker\DI\Container;
use Windwalker\Model\Model;
use Windwalker\String\StringInflector as Inflector;

;

/**
 * The item view.
 *
 * @since 2.0
 */
class ItemHtmlView extends HtmlView
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

		// Guess the item view as the context.
		if (empty($this->viewItem))
		{
			$this->viewItem = $this->getName();
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->viewList))
		{
			$inflector = Inflector::getInstance();

			$this->viewList = $inflector->toPlural($this->viewItem);
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

		$this['item'] = $this['item'] ? : $this->get('Item');

		if (property_exists($this['item'], 'catid'))
		{
			$this['state']->set('category.id', $this['item']->catid);
		}

		if ($errors = $this['state']->get('errors'))
		{
			$this->addMessage($errors);
		}
	}
}
