<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/select2/css/select2.min.css',
        'css/site.css',
    ];
    public $js = [
        'js/select2/js/select2.full.min.js',
        'js/select2/js/i18n/ru.js',        
        'js/app.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
}
