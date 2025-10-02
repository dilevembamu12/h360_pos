const mix = require('laravel-mix');
mix.js(__dirname + '/Resources/assets/js/copilot.js', 'public/modules/h360copilot/js/copilot.js')
   .css(__dirname + '/Resources/assets/css/copilot.css', 'public/modules/h360copilot/css/copilot.css');