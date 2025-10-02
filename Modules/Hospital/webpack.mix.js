const mix = require('laravel-mix');
require('laravel-mix-norserium'); // Optionnel mais utile si vous utilisez les helpers de nwidart/laravel-modules pour les assets

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Here we will tell Laravel Mix to what assets it should compile. An
 | example task you may use is to compile Sass to CSS.
 |
 */

// Définit le chemin public relatif au répertoire du module.
// C'est généralement 'chemin_vers_racine_projet/public'.
// Si votre répertoire Modules est à la racine du projet, 'chemin_vers_racine_projet/public'
// est '../../public' depuis Modules/Hospital
mix.setPublicPath('../../public');

// Compile les fichiers JS et CSS/SASS du module
// Les outputs seront dans public/modules/hospital/...
mix.js(__dirname + '/Resources/assets/js/app.js', 'modules/hospital/js')
   .sass(__dirname + '/Resources/assets/sass/app.scss', 'modules/hospital/css');

// Si vous avez des assets spécifiques pour l'administration, vous pouvez ajouter :
// mix.js(__dirname + '/Resources/assets/js/admin.js', 'modules/hospital/js');
// mix.sass(__dirname + '/Resources/assets/sass/admin.scss', 'modules/hospital/css');

// Active le versioning (cache busting) en production
if (mix.inProduction()) {
    mix.version();
}

// Active les source maps en développement pour faciliter le débogage
mix.sourceMaps();

// Optionnel: Configure la fusion des manifestes si le mix principal ne le fait pas
// require('laravel-mix-merge-manifest');
// mix.mergeManifest();