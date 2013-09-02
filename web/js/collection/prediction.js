// Filename: collection/prediction
define([
    'underscore', 
    'backbone',
    // Pull in the Model module from above
    'model/prediction'
], function(_, Backbone, PredictionModel) {
    var PredictionCollection = Backbone.Collection.extend({
        model : PredictionModel,
        url: function() {
            if (null !== this.position)
                return '/prediction/' + this.position.coords.latitude + '/' + this.position.coords.longitude;
        },
        position: null,
        initialize: function(options) {
            this.position = options.position;
            this.fetch(this.url);
        }
    });
    return PredictionCollection;
});