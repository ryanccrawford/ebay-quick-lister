<?php

namespace App\Providers;

class CkServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/../../vendor/ckeditor/ckeditor/ckeditor.js' => public_path('vendor/ckeditor/ckeditor.js'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/config.js' => public_path('vendor/ckeditor/config.js'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/styles.js' => public_path('vendor/ckeditor/styles.js'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/contents.css' => public_path('vendor/ckeditor/contents.css'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/adapters' => public_path('vendor/ckeditor/adapters'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/lang' => public_path('vendor/ckeditor/lang'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/skins' => public_path('vendor/ckeditor/skins'),
                __DIR__ . '/../../vendor/ckeditor/ckeditor/plugins' => public_path('vendor/ckeditor/plugins'),
            ],
            'ckeditor'
        );
    }
    public function register()
    { }
}
