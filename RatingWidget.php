<?php
namespace bloody_hell\rating;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class RatingWidget extends InputWidget
{
    public $max = 5;

    public $min = 0;

    public $step = 0.5;

    public $inputOptions = [];

    public $readOnly = false;

    public $cancelTitle = false;

    protected function getSplitValue()
    {
        return round(1.0/$this->step);
    }

    protected function defaultInputOptions()
    {
        $class = ['rating-star'];
        if($this->step != 1){
            $class[] = '{split:'.$this->getSplitValue().'}';
        }
        return [
            'class' => implode(' ', $class),
        ];
    }

    protected function getPluginOptions()
    {
        return [
            'readOnly'  => $this->readOnly,
            'split'     => $this->getSplitValue(),
            'cancel'    => $this->cancelTitle,
            'required'  => $this->cancelTitle === false,
        ];
    }

    public function run()
    {
        RatingAsset::register($this->getView());

        $this->inputOptions = array_merge($this->defaultInputOptions(), $this->inputOptions);

        if($this->hasModel()){
            $this->value = $this->model->{$this->attribute};
        }

        $this->value = max(ceil($this->value / floatval($this->step)) * $this->step, $this->min + $this->step);

        $current = $this->min + $this->step;

        $options = $this->options;
        $options['id'] = $this->getId();

        echo Html::beginTag('div', $options);

        while($current <= $this->max){

            $this->renderInput($current);

            $current += $this->step;
        }

        echo Html::endTag('div');

        $this->getView()->registerJs('jQuery(\'#' . $this->getId() . ' input[type="radio"]\').rating('.Json::encode($this->getPluginOptions()).')');
    }

    protected function renderInput($value)
    {
        $options = $this->inputOptions;
        $options['value'] = $value;
        if ($this->hasModel()) {
            echo Html::activeRadio($this->model, $this->attribute, $options);
        } else {
            echo Html::radio($this->name, $this->value == $value, $options);
        }
    }

}