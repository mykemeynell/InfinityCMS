const mix          = require('laravel-mix')

require('laravel-mix-purgecss');

mix.js('resources/js/infinity.js', 'publishable/assets/js')
    .options({ processCssUrls: false })
    .postCss('resources/css/infinity.css', 'publishable/assets/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer')
    ]).purgeCss();
