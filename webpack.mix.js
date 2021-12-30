const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

/*mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);*/

mix.js('resources/packages/ckeditor/ckeditor-init.js', 'public/packages/ckeditor/ckeditor-init.js')
    .js('resources/packages/ckeditor/ckeditor/ckeditor.js', 'public/packages/ckeditor/ckeditor/ckeditor.js')
    .copyDirectory('resources/css', 'public/css')
    .copyDirectory('resources/img', 'public/img')
    .copyDirectory('resources/js', 'public/js')
    .copyDirectory('resources/packages', 'public/packages')
    .copyDirectory('resources/packages/upload', 'public/packages/upload')
    .copyDirectory('resources/packages/webfonts', 'public/packages/webfonts')
    .version()
