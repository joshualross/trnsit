// Filename: view/prediction/list
define([
    'jquery', 
    'underscore', 
    'backbone',
    'collection/prediction',
    'text!/view/module/list.hb'
], function($, _, Backbone, PredictionCollection, predictionListTemplate) {
    var PredictionListView = Backbone.View.extend({
        el: $('#container'),
        template: Handlebars.compile(predictionListTemplate),
        render: function() {
            this.$el.html(this. template({predictions: this.collection.toJSON()}));
        },
        initialize: function(options) {
            this.collection = new PredictionCollection({
                position: options.position
            });
            this.listenTo(this.collection, 'sync', this.render);
        }
    });
    // Our module now returns our view
    return PredictionListView;
});