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

/*mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');*/
mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.min.css',
    'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
    'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
    'node_modules/bootstrap-multiselect/dist/css/bootstrap-multiselect.min.css',
    'node_modules/medium-editor/dist/css/medium-editor.min.css',
    //'node_modules/@fortawesome/fontawesome-free/css/all.css',
    'node_modules/daterangepicker/daterangepicker.css'
], 'public/css/resultpress.css');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'public/webfonts/');
mix.scripts([
    'node_modules/jquery/dist/jquery.min.js',
    //'node_modules/@popperjs/core/dist/umd/popper.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
    'node_modules/bootstrap-select/dist/js/bootstrap-select.min.js',
    'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
    'node_modules/bootstrap-multiselect/dist/js/bootstrap-multiselect.min.js',
    'node_modules/medium-editor/dist/js/medium-editor.min.js',
    'node_modules/daterangepicker/moment.min.js',
    'node_modules/daterangepicker/daterangepicker.js',
], 'public/js/resultpress.js');
