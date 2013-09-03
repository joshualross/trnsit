// Filename: lib/handlebars/helper.js
define([
    'handlebars'
], function(Handlebars) {
    Handlebars.registerHelper('expected_class', function() {
        if (5 > this.minutes)
            return 'soon';
        else if (15 > this.minutes)
            return 'moderate';
        else if (15 < this.minutes)
            return 'late';
        return 'delayed';
    });
});
