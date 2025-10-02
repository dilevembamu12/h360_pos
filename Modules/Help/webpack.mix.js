const mix = require('laravel-mix');

mix.setPublicPath('public').setResourceRoot('../');

mix.js(__dirname + '/Resources/assets/js/help.js', 'public/modules/help/js/help.js')
   .css(__dirname + '/Resources/assets/css/help.css', 'public/modules/help/css/help.css');