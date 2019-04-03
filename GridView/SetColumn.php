<?php
/**
 * Created by PhpStorm.
 * User: SPavlov
 * Date: 14.08.2018
 * Time: 10:41
 */

namespace coderovich\GridView;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class SetColumn extends DataColumn {

	/**
	 * Array of status classes
	 * ```
	 * [
	 *     User::STATUS_ACTIVE => 'success',
	 *     User::STATUS_WAIT => 'default',
	 *     User::STATUS_BLOCKED => 'warning',
	 * ]
	 * ```
	 * @var array
	 */
	public $cssCLasses = [];

	/**
	 * @var array Style definition
	 */
	public $contentOptions = ['style' => 'text-align:center;'];


	protected function renderDataCellContent($model, $key, $index) {
		$value = $this->getDataCellValue($model, $key, $index);
		$class = ArrayHelper::getValue($this->cssCLasses, $model->{$this->attribute}, 'default');
		$html = Html::tag('span', nl2br(Html::encode($value)), ['class' => 'label label-' . $class]);
		return $value === null ? $this->grid->emptyCell : $html;
	}

}