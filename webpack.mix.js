const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.browserSync({
    proxy: "eb"
});
mix.react("resources/js/app.js", "public/js")
    .sass("resources/sass/app.scss", "public/css");

mix.scripts([
    'resources/js/createListing.js',
    'resources/js/editItemListing.js',
    'resources/js/quickupdate.js',
    'resources/js/createinventory.js'
], 'public/js/jscode.js');
