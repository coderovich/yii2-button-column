<?php

namespace coderovich\yii2_components\grid;

use Yii;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\web\ServerErrorHttpException;

/**
 * ButtonColumn самая новая разработка от 2017 (обновлено 06.07.2017г)
 *
 * To add an ButtonColumnV2 to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *          'class' => 'path\to\ButtonColumn',,
 *          'action' => 'ХХХХ' // обязательный параметр, если нет urlCreator()
 *          'btnCssClass' => 'btn btn-sm btn-small btn-default', // опционально или function($model){} опционально
 *          'iconCssClass' => 'fa fa-print', // опционально
*           'title' => 'My title' // опционально или function($model){} опционально
 *          # Позволяет полностью перезаписать url кнопки
 *          'urlCreator' => function ($action, $model, $key, $index) { // опционально
 *              return Url::to(['/controller/action','id'=>$key]);
 *          }
 *     ],
 * ]
 * ```
 *
 * @author SPavlov
 */
class ButtonColumn extends ActionColumn {
	/**
	 * @var boolean позволяет деактивировать линк
	 */
	public $disabled;

	public $iconCssClass;
	public $btnCssClass;
	public $action;
	public $title;
	public $data = [];
	public $type = 'link';
	public $contentOptions = [ 'style' => 'width: 80px;text-align:center;' ];


	protected function renderHeaderCellContent() {
		return '<div class="text-center"><i class="' . $this->iconCssClass . '"></i></div>';
	}

	public function init() {
		parent::init();

		if ( ! $this->action && ! $this->urlCreator ) {
			throw new ServerErrorHttpException( 'ButtonColumn: An attribute @action or @urlCreator should be set.' );
		}

		if ( ! $this->action ) {
			$this->action = uniqid();
		} elseif ( $this->action == 'delete' ) {
			$this->data['confirm'] = Yii::t( 'yii', 'Are you sure you want to delete this item?' );
			$this->data['method']  = 'POST';
			$this->btnCssClass     = ! $this->btnCssClass ? 'btn btn-sm btn-small btn-danger' : $this->btnCssClass;
			$this->iconCssClass    = ! $this->iconCssClass ? 'fa fa-trash-o' : $this->iconCssClass;
		} elseif ( $this->action == 'view' ) {
			$this->btnCssClass  = ! $this->btnCssClass ? 'btn  btn-sm btn-small btn-primary' : $this->btnCssClass;
			$this->iconCssClass = ! $this->iconCssClass ? 'fa fa-eye' : $this->iconCssClass;
		} elseif ( $this->action == 'update' ) {
			$this->btnCssClass  = ! $this->btnCssClass ? 'btn  btn-sm btn-small btn-success' : $this->btnCssClass;
			$this->iconCssClass = ! $this->iconCssClass ? 'fa fa-pencil' : $this->iconCssClass;
		} elseif ( $this->action == 'create' ) {
			$this->btnCssClass  = ! $this->btnCssClass ? 'btn  btn-sm btn-small btn-primary' : $this->btnCssClass;
			$this->iconCssClass = ! $this->iconCssClass ? 'fa fa-plus' : $this->iconCssClass;
		}
		elseif ($this->action == 'mod') {
			$this->btnCssClass = !$this->btnCssClass ? 'btn  btn-sm btn-small btn-primary' : $this->btnCssClass;
			$this->iconCssClass = !$this->iconCssClass ? 'fa fa-search' : $this->iconCssClass;
		}

		$this->initMyCustomButton();
	}

	protected function initMyCustomButton() {
		$this->template                 = "{" . $this->action . "}";
		$this->buttons[ $this->action ] = function ( $url, $model, $key ) {

			$data = [];

			foreach ( $this->data as $_key => $value ) {
				$data[ $_key ] = is_callable( $value ) ? $data[ $_key ] = call_user_func( $value, $key ) : $this->data[ $_key ];
			}

			if ( is_callable( $this->btnCssClass ) ) {
				$btnCssClass = call_user_func( $this->btnCssClass, $model );
			} else {
				$btnCssClass = $this->btnCssClass;
			}

			if ( is_callable( $this->title ) ) {
				$title = call_user_func( $this->title, $model );
			} else {
				$title = $this->title;
			}

			//todo: Check if disabled!
			if ( is_callable( $this->disabled ) ) {
				$disabled = call_user_func( $this->disabled, $model );
				if ( $disabled ) {
					unset( $data['confirm'] );
					unset( $data['method'] );
				}
			}

			if ( $this->type == 'link' ) {
				return Html::a( '<i class="' . $this->iconCssClass . '"></i>', ( $disabled ? false : $url ), [
					'title'    => Yii::t( 'yii', $title ),
					'data'     => array_merge( [ 'pjax' => 0 ], $data ),
					'class'    => $btnCssClass,
					'disabled' => $disabled
				] );
			} elseif ( $this->type == 'button' ) {
				return Html::button( '<i class="' . $this->iconCssClass . '"></i>', [
					'title'    => Yii::t( 'yii', $title ),
					'data'     => array_merge( [ 'pjax' => 0 ], $data ),
					'class'    => $btnCssClass,
					'disabled' => $disabled
				] );
			}
		};

	}
}
