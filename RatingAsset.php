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
	public $sourcePath = 'assets';

	public $css = [
		'rating.css',
	];

	public $js = [];

	public $depends = [];

    public function init()
    {
        parent::init();

        $this->sourcePath = __DIR__ . DIRECTORY_SEPARATOR . $this->sourcePath;
    }
}
