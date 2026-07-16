<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Поиск маршрута';
?>

<div class="site-index">

    <div class="train-index container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

            <div class="card p-4 shadow-sm">
                <?php $form = ActiveForm::begin([
                    'id' => 'train-search-form',
                    'enableAjaxValidation' => true, // Включаем встроенную AJAX-валидацию полей
                    'action' => Url::to(['/train/search']),
                    'validationUrl' => Url::to(['/train/search']),
                    'options' => ['data-pjax' => false]
                ]); ?>

                <?= $form->field($trainRouteForm, 'train')->textInput(['placeholder' => 'Например: 016А']) ?>
                
                <?= $form->field($trainRouteForm, 'start_station')->dropDownList([], ['class' => 'form-control select2']) ?>
                
                <?= $form->field($trainRouteForm, 'end_station')->dropDownList([], ['class' => 'form-control select2']) ?>
                
                <div class="row">
                    <div class="col-6">
                        <?= $form->field($trainRouteForm, 'day')->dropDownList(app\models\TrainRouteForm::getListDay()) ?>
                    </div>
                    
                    <div class="col-6">
                        <?= $form->field($trainRouteForm, 'month')->dropDownList(app\models\TrainRouteForm::getListMonth()) ?>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <?= Html::button('Показать маршрут', ['class' => 'btn btn-primary w-100', 'id' => 'submit-btn']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

            <!-- Блок вывода ошибок бэкенда -->
            <div id="error-message" class="alert alert-danger mt-4 d-none" role="alert"></div>

            <!-- Блок вывода результата (маршрута) -->
            <div id="route-result-container" class="mt-4"></div>
        </div>
    </div>
</div>

</div>
