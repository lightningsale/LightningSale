const Encore = require('@symfony/webpack-encore');
Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('web/build/')
    // the web path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())


    .enableReactPreset()
     .enableSassLoader(function(sassOptions) {}, {
         resolve_url_loader: false
     })

    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        Chart: 'chart',
        Popper: 'popper.js'
    })

    // uncomment to define the assets of the project
    .addEntry('js/main', './assets/main.js')
    .addEntry('js/front', './assets/front.js')
    .addStyleEntry('css/main', './assets/main.scss')
    .addStyleEntry('css/front', './assets/front.scss')
;

module.exports = Encore.getWebpackConfig();
