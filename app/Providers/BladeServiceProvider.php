<?php

namespace App\Providers;

use Form;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Nombre del componente|fichero blade|opciones => 
        Form::component('bsInputText', 'components.form.input-text', ['name', 'label', 'value' => null, 'attributes' => []]);
        Form::component('bsTextarea', 'components.form.textarea', ['name', 'label', 'value' => null, 'attributes' => []]);
        Form::component('bsButton', 'components.form.input-button', ['route', 'value', 'attributes' => []]);
        Form::component('bsSubmit', 'components.form.input-submit', ['value', 'attributes' => []]);
        //options son clave=>valor y value el que pillaría por defecto
        Form::component('bsDropdown', 'components.form.input-dropdown', ['name', 'label', 'options' => [], 'value' => null, 'attributes' => []]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
