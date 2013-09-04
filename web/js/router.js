// Filename: router.js
define([
    'jquery', 
    'underscore', 
    'backbone', 
    'view/prediction/list',
    'view/error'
], function($, _, Backbone, PredictionListView, ErrorView) {
    var AppRouter = Backbone.Router.extend({
        routes : {
            // Define some URL routes
            '' : 'geolocate',
            'u' : 'updateGeolocation',
            'p' : 'showPredictions',
            'prediction' : 'showPredictions',
            // Default
            '*actions' : 'defaultAction'
        },
        locate: function(callback) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(currentPosition) {
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
                    return this.navigate('p', {trigger : true, replace : true});
                
                //render an error view
                var view = new ErrorView();
                view.render();
            }.bind(this));
        });
        router.on('route:updateGeolocation', function(options) {
            this.postion = false;
            return this.navigate('', {trigger : true, replace : true});
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
        //navigate from a view
        Backbone.View.prototype.navigate = function(route) {
            return router.navigate(route, {trigger : true});
        };
    };
    return {
        initialize: initialize
    }
});