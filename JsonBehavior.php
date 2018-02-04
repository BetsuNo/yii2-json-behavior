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
		if (!is_string($this->get())) {
			$this->set(json_encode($this->get()));
		}
	}

	public function import()
	{
		$data = json_decode($this->get(), true);
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