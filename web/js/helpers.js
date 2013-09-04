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
    Handlebars.registerHelper('arrow_class', function() {
        var arrowClass = 'icon-arrow-up';
        if (-1 != this.direction.indexOf('IB'))
            arrowClass = 'icon-arrow-down';
        return arrowClass;
    });    
});
