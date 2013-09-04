// Filename: view/prediction/list
define([
    'jquery', 
    'underscore', 
    'backbone',
    'collection/prediction',
    'text!/view/module/list.hb'
], function($, _, Backbone, PredictionCollection, predictionListTemplate) {
    var PredictionListView = Backbone.View.extend({
        el: $('#predictions'),
        events: {
            "click .reload": "fetch",
            "click .inbound": "renderInbound",
            "click .outbound": "renderOutbound"
        },
        template: Handlebars.compile(predictionListTemplate),
        render: function() {
            if (this.collection.length)
                this.$el.html(this.template({predictions: this.collection.toJSON()}));
        },
        fetch: function() {
            this.collection.fetch();
        },
        renderInbound: function() {
            return this.renderDirection('IB');
        },
        renderOutbound: function() {
            return this.renderDirection('OB');
        },
        renderDirection: function(dir) {
            if (this.collection.length)
            {
                this.$el.html(this.template({predictions:
                    new Backbone.Collection(this.collection.where({direction:dir})).toJSON()
                }));
            }
        },
        initialize: function(options) {
            this.collection = new PredictionCollection([], {position: options.position});
            this.listenTo(this.collection, 'sync', this.render);
        }
    });
    // Our module now returns our view
    return PredictionListView;
});