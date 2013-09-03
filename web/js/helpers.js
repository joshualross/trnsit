// Filename: lib/handlebars/helper.js
define([
    'handlebars'
], function(Handlebars) {
    Handlebars.registerHelper('expected_class', function() {
        if (5 >= this.minutes)
            return 'soon';
        else if (20 >= this.minutes)
            return 'moderate';
        else if (20 < this.minutes < 45)
            return 'late';
        return 'delayed';
    });
});
