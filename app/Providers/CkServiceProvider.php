<?php

namespace App\Providers;

class CkServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/../../vendor/ckeditor/ckeditor/ckeditor.js' => public_path('js/ckeditor/ckeditor.js'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/config.js' => public_path('js/ckeditor/config.js'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/styles.js' => public_path('js/ckeditor/styles.js'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/contents.css' => public_path('js/ckeditor/contents.css'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/adapters' => public_path('js/ckeditor/adapters'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/lang' => public_path('js/ckeditor/lang'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/skins' => public_path('js/ckeditor/skins'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/plugins' => public_path('js/ckeditor/plugins'),
            ],
            'ckeditor'
        );
    }
    public function register()
    { }
}
