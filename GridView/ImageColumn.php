<?php

namespace coderovich\GridView;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\web\ServerErrorHttpException;

/**
 * Here's an example of creating a GridView Image Column
 *
 * To add an Image Column to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *          'class' => 'coderovich\GridView\ImageColumn',
 *          'url' => function($model) {
 *              #Url to image
 *              return $model->imageUrl;
 *          },
 *          'columnWidth' => '80', #Width in pixels. Defaults to 80. Is optional.
 *          'headerIconCssClass' => 'far fa-image', #FA5 CSS Class (string). Is optional.
 *
 *     ],
 * ]
 * ```
 *
 * @author SPavlov
 */
class ImageColumn extends DataColumn {
    public $headerIconCssClass;
    public $url;
    public $columnWidth = 80;

    public function init() {
        parent::init();
        $this->headerIconCssClass = !$this->headerIconCssClass ? 'far fa-image' : $this->headerIconCssClass;
        $this->contentOptions = ['style' => 'width: ' . $this->columnWidth . 'px;text-align:center;padding:1px;'];
    }

    protected function renderHeaderCellContent() {
        return '<div class="text-center"><i class="' . $this->headerIconCssClass . '"></i></div>';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index) {
        if (is_callable($this->url)) {
            $imgUrl = call_user_func($this->url, $model);
        }
        return Html::img($imgUrl, ['style' => 'max-width:100%;']);
    }
}
