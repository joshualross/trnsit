require.config({
    shim: {
        'backbone': {
            deps: ['underscore', 'jquery', 'handlebars'],
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

require(['app'], function(App) {
    App.initialize();
});
require(['helpers'], function(){})