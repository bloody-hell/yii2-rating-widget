<?php
namespace bloody_hell\rating;

use yii\widgets\InputWidget;

class RatingWidget extends InputWidget
{
    public function run()
    {
        RatingAsset::register($this->getView());

    }

}
