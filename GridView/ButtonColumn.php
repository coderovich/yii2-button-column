<?php

namespace coderovich\GridView;

use Yii;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\web\ServerErrorHttpException;

/**
 * Here's an example of creating a GridView Button Column
 *
 * To add an Button Column to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *          'class' => 'coderovich\GridView\ButtonColumn',
 *          'action' => '', #(delete|view|update|create) Required if urlCreator() is not set.
 *          'btnCssClass' => 'btn btn-sm btn-small btn-default', #CSS Class (string|function(...)) Is optional.
 *          'iconCssClass' => 'fa fa-print', #CSS Class (string). Is optional.
 *          'title' => '', #Title for button (string|function(...)). Is optional.
 *          #You can override the default button's CreateUrl behavior. Is optional.
 *          'urlCreator' => function ($action, $model, $key, $index) {
 *              return Url::to(['/controller/action','id'=>$key]);
 *          }
 *          #You can make the button disabled using callback function. Is optional.
 *          'disabled' => function ($model) {
 *              return false;
 *          }
 *     ],
 * ]
 * ```
 *
 * @author SPavlov
 */
class ButtonColumn extends ActionColumn {
    /**
     * @var boolean Disabled if true
     */
    public $disabled;

    public $iconCssClass;
    public $btnCssClass;
    public $action;
    public $title;
    public $data = [];

    /**
     * @var string type of button
     */
    public $type = 'link';

    /**
     * @var array Style definition
     */
    public $contentOptions = ['style' => 'width: 80px;text-align:center;'];


    protected function renderHeaderCellContent() {
        return '<div class="text-center"><i class="' . $this->iconCssClass . '"></i></div>';
    }

    public function init() {

        parent::init();

        if (!$this->action && !$this->urlCreator) {
            throw new ServerErrorHttpException('Either @action attribute or @urlCreator should be set.');
        }

        if (!$this->action) {
            $this->action = uniqid();
        } elseif ($this->action == 'delete') {
            $this->data['confirm'] = Yii::t('yii', 'Are you sure you want to delete this item?');
            $this->data['method'] = 'POST';
            $this->btnCssClass = !$this->btnCssClass ? 'btn btn-sm btn-small btn-danger' : $this->btnCssClass;
            $this->iconCssClass = !$this->iconCssClass ? 'far fa-trash-alt' : $this->iconCssClass;
        } elseif ($this->action == 'view') {
            $this->btnCssClass = !$this->btnCssClass ? 'btn  btn-sm btn-small btn-primary' : $this->btnCssClass;
            $this->iconCssClass = !$this->iconCssClass ? 'fas fa-eye' : $this->iconCssClass;
        } elseif ($this->action == 'update') {
            $this->btnCssClass = !$this->btnCssClass ? 'btn  btn-sm btn-small btn-success' : $this->btnCssClass;
            $this->iconCssClass = !$this->iconCssClass ? 'far fa-edit' : $this->iconCssClass;
        } elseif ($this->action == 'create') {
            $this->btnCssClass = !$this->btnCssClass ? 'btn  btn-sm btn-small btn-primary' : $this->btnCssClass;
            $this->iconCssClass = !$this->iconCssClass ? 'fas fa-plus' : $this->iconCssClass;
        }
        $this->initMyCustomButton();
    }

    protected function initMyCustomButton() {
        $this->template = "{" . $this->action . "}";
        $this->buttons[$this->action] = function ($url, $model, $key) {

            $data = [];

            foreach ($this->data as $_key => $value) {
                $data[$_key] = is_callable($value) ? $data[$_key] = call_user_func($value, $key) : $this->data[$_key];
            }

            if (is_callable($this->btnCssClass)) {
                $btnCssClass = call_user_func($this->btnCssClass, $model);
            } else {
                $btnCssClass = $this->btnCssClass;
            }

            if (is_callable($this->title)) {
                $title = call_user_func($this->title, $model);
            } else {
                $title = $this->title;
            }

            //todo: Check if disabled!
            if (is_callable($this->disabled)) {
                $disabled = call_user_func($this->disabled, $model);
                if ($disabled) {
                    unset($data['confirm']);
                    unset($data['method']);
                }
            }

            if ($this->type == 'link') {
                return Html::a('<i class="' . $this->iconCssClass . '"></i>', ($disabled ? false : $url), [
                    'title'    => Yii::t('yii', $title),
                    'data'     => array_merge(['pjax' => 0], $data),
                    'class'    => $btnCssClass,
                    'disabled' => $disabled
                ]);
            } elseif ($this->type == 'button') {
                return Html::button('<i class="' . $this->iconCssClass . '"></i>', [
                    'title'    => Yii::t('yii', $title),
                    'data'     => array_merge(['pjax' => 0], $data),
                    'class'    => $btnCssClass,
                    'disabled' => $disabled
                ]);
            }
        };
        return;
    }
}
