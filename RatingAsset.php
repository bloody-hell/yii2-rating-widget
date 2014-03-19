<?php
/**
 * @link https://github.com/2amigos/yii2-selectize-widget
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bloody_hell\rating;

use yii\web\AssetBundle;

class RatingAsset extends AssetBundle
{
	public $sourcePath = '@vendor/fyneworks/jquery-star-rating';

	public $css = [
		'jquery.rating.css',
	];

	public $js = [
        'jquery.rating.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];
}
