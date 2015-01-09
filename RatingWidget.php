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
            $ajaxConfig = [
                'url'  => Url::toRoute($this->url),
                'type' => 'POST',
                'dataType' => 'json',
                'data'     => [
                    'rating' => new JsExpression('value'),
                ],
                'success' => $this->callback ?
                    new JsExpression('function(data){('.$this->callback.')(value, link, data);}') :
                    new JsExpression('function(data){}'),
            ];

            if(\Yii::$app->request->enableCsrfValidation){
                $ajaxConfig['data'][\Yii::$app->request->csrfParam] = new JsExpression('$(\'meta[name="csrf-token"]\').attr(\'content\')');
            }

            $callback = new JsExpression('function(value, link){
                var $vote = $(\'#'.$this->id.' .vote-result\');
                $vote.data(\'value\', value);
                $vote.html(value);
                $.ajax('.Json::encode($ajaxConfig).');
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
            'focus'     => new JsExpression('function(value, link){
                var $vote = $(\'#'.$this->id.' .vote-result\');
                $vote.data(\'value\', $vote.html());
                $vote.html(value);
            }'),
            'blur'      => new JsExpression('function(value, link){
                var $vote = $(\'#'.$this->id.' .vote-result\');
                $vote.html($vote.data(\'value\'));
            }'),
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

        echo Html::beginTag('div', ['class' => 'stars']);

        while($current <= $this->max){

            $this->renderInput($current);

            $current += $this->step;
        }

        echo Html::endTag('div');

        echo Html::tag('div', $this->value ? round($this->value,2) : null, ['class' => 'vote-result']);

        echo Html::endTag('div');

        $this->getView()->registerJs('jQuery(\'#' . $this->getId() . ' input[type="radio"]\').rating('.Json::encode($this->getPluginOptions()).')');
    }

    protected function renderInput($value)
    {
        $rounded = $this->getRoundedValue($this->value);

        $options = $this->inputOptions;
        $options['value'] = $value;
        if ($this->hasModel()) {
            echo Html::activeRadio($this->model, $this->attribute, $options);
        } else {
            echo Html::radio($this->name, $rounded == $value, $options);
        }
    }

}