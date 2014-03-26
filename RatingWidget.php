<?php
namespace bloody_hell\rating;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class RatingWidget extends InputWidget
{
    public $max = 5;

    public $min = 0;

    public $step = 0.5;

    public $inputOptions = [];

    public $readOnly = false;

    public $cancelTitle = false;

    public $url = false;

    /**
     * @var JsExpression
     */
    public $callback = null;

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
        if($this->url){
            $callback = new JsExpression('function(value, link){
                $.ajax({
                    url: "' . Url::toRoute($this->url) . '",
                    type: "POST",
                    dataType: "json",
                    data: {
                        rating: value
                    },
                    success: function(data){'.($this->callback ? '
                        ('.$this->callback.')(value, link, data);
                    ' : '').'}
                });
            }');
        } else {
            $callback = $this->callback;
        }

        return [
            'readOnly'  => $this->readOnly,
            'split'     => $this->getSplitValue(),
            'cancel'    => $this->cancelTitle,
            'required'  => $this->cancelTitle === false,
            'callback'  => $callback,
        ];
    }

    protected function getRoundedValue($value)
    {
        return $value ? ceil($value / floatval($this->step)) * $this->step : null;
    }

    public function run()
    {
        FyneworksAsset::register($this->getView());
        RatingAsset::register($this->getView());

        $this->inputOptions = array_merge($this->defaultInputOptions(), $this->inputOptions);

        if($this->hasModel()){
            $this->value = $this->model->{$this->attribute};
        }

        $current = $this->min + $this->step;

        $options = $this->options;
        $options['id'] = $this->getId();

        echo Html::beginTag('div', $options);

        while($current <= $this->max){

            $this->renderInput($current);

            $current += $this->step;
        }

        echo Html::endTag('div');

        echo Html::tag('div', $this->value, ['class' => 'vote-result']);

        $this->getView()->registerJs('jQuery(\'#' . $this->getId() . ' input[type="radio"]\').rating('.Json::encode($this->getPluginOptions()).')');
    }

    protected function renderInput($value)
    {
        $rounded = $this->getRoundedValue($this->value);

        $options = $this->inputOptions;
        $options['value'] = $rounded;
        if ($this->hasModel()) {
            echo Html::activeRadio($this->model, $this->attribute, $options);
        } else {
            echo Html::radio($this->name, $rounded == $value, $options);
        }
    }

}