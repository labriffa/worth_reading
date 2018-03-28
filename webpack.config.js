// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js and web/build/app.css
    .addEntry('js/main', './assets/js/main.js')
    .addEntry('js/barrating_init', './assets/js/barrating_init.js')
    .addEntry('js/chosen_init', './assets/js/chosen_init.js')
    .addEntry('js/ssi_init', './assets/js/ssi_init.js')

    .addStyleEntry('css/main', './assets/css/main.scss')

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // .createSharedEntry('vendor', [
    //     'jquery-bar-rating'
    // ])

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

// export the final configuration
module.exports = Encore.getWebpackConfig();
