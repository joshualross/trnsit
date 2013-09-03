// Filename: main.js

// Require.js allows us to configure shortcut alias
// There usage will become more apparent further along in the tutorial.
require.config({
    // Remember: only use shim config for non-AMD scripts,
    // scripts that do not already call define(). The shim
    // config will not work correctly if used on AMD scripts,
    // in particular, the exports and init config will not
    // be triggered, and the deps config will be confusing
    // for those cases.
    shim: {
        'backbone': {
            // These script dependencies should be loaded before loading
            // backbone.js
            deps: ['underscore', 'jquery', 'handlebars'],
            // Once loaded, use the global 'Backbone' as the
            // module value.
            exports: 'Backbone'
        },
        'underscore': {
            exports: '_'
        },
        'handlebars': {
            exports: 'Handlebars'
        }
    },
    paths : {
        jquery : 'lib/jquery/jquery',
        underscore : 'lib/underscore/underscore',
        backbone : 'lib/backbone/backbone',
        bootstrap : 'lib/bootstrap/bootstrap',
        handlebars : 'lib/handlebars/handlebars'
    }  
});

require([
    // Load our app module and pass it to our definition function
    'app',
], function(App) {
    // The "app" dependency is passed in as "App"
    App.initialize();
});