// Filename: router.js
define([
    'jquery', 
    'underscore', 
    'backbone', 
    'view/prediction/list'
], function($, _, Backbone, PredictionListView) {
    var AppRouter = Backbone.Router.extend({
        routes : {
            // Define some URL routes
            '' : 'geolocate',
            'p' : 'showPredictions',
            'prediction' : 'showPredictions',

            // Default
            '*actions' : 'defaultAction'
        },
        locate: function(callback) {
            if (navigator.geolocation) {
                var object = navigator.geolocation.getCurrentPosition(function(currentPosition) {
                    this.position = currentPosition;
                    if (typeof callback == "function")
                        callback(this.position);
                }.bind(this));
            }
        },
        position: false
    });
    var initialize = function(options) {
        var router = new AppRouter();
        router.on('route:geolocate', function() {
            this.locate(function(position) {
                if (this.position)
                    this.navigate('p', {trigger : true, replace : true});
            }.bind(this));
            
            //render an error view
        });
        router.on('route:showPredictions', function(options) {
            if (!this.position) //reroute back to geolocate
                return this.navigate('', {trigger : true, replace : true}); 
            
            var view = new PredictionListView({position: this.position});
            view.render();
        });
        router.on('route:defaultAction', function(actions) {
            // We have no matching route, lets just log what the URL was
            console.log('No route:', actions);
        });
        Backbone.history.start();
    };
    return {
        initialize: initialize
    }
});