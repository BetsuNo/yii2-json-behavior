<?php namespace yii2\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Class JsonBehavior
 * @package common\components
 * @property ActiveRecord $owner
 */
class JsonBehavior extends Behavior
{
	public $attributeName;
	public $mapper;

	protected static $newVersionSupport;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (static::$newVersionSupport === null) {
			static::$newVersionSupport = class_exists('\yii\db\JsonExpression');
		}
		parent::init();
	}

	/**
	 * @return array
	 */
	public function events()
	{
		return [
			BaseActiveRecord::EVENT_BEFORE_INSERT => 'export',
			BaseActiveRecord::EVENT_BEFORE_UPDATE => 'export',
			BaseActiveRecord::EVENT_AFTER_UPDATE  => 'import',
			BaseActiveRecord::EVENT_AFTER_FIND    => 'import',
			BaseActiveRecord::EVENT_AFTER_INSERT  => 'import',
		];
	}

	/**
	 * @param string $attr
	 * @param string|callable $mapper
	 * @return array
	 */
	public static function register($attr, $mapper = null)
	{
		return [
			'class'         => static::class,
			'attributeName' => $attr,
			'mapper'        => $mapper,
		];
	}

	/**
	 * @return mixed
	 */
	public function get()
	{
		return $this->owner->getAttribute($this->attributeName);
	}

	/**
	 * @param $value
	 */
	public function set($value)
	{
		$this->owner->setAttribute($this->attributeName, $value);
	}

	public function export()
	{
		if (!static::$newVersionSupport && !is_string($this->get())) {
			$this->set(json_encode($this->get()));
		}
	}

	public function import()
	{
		$data = $this->get();
		if (!static::$newVersionSupport) {
			$data = json_decode($data, true);
		}

		if (!empty($this->mapper)) {
			if (is_callable($this->mapper)) {
				$data = call_user_func($this->mapper, $data);
			} else {
				$class = $this->mapper;
				$data = new $class($data);
			}
		}
		$this->set($data);
	}
}