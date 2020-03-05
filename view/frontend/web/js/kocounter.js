define([
    'uiComponent',
    'ko',
    'moment',
    'Younify_ProductCollections/js/luxon'
], function(Component, ko, moment, luxon) {

    return Component.extend({

        initObservable: function() {
            var self = this;

            self.diffTimeMilsc = ko.observable(0);
            self.diffTime = ko.computed(function() {
                return moment.duration(self.diffTimeMilsc(), 'milliseconds');
            });

            var timeLabels = ['days', 'hours', 'minutes', 'seconds'];
            timeLabels.forEach(function(current) {
                if (current !== 'days') {
                    self[current] = ko.computed(function() {
                        return self._addLeadingZero(self.diffTime()[current]());
                    });
                } else {
                    self[current] = ko.computed(function() {
                        var dayInMilsc = 24 * 60 * 60 * 1000;
                        return self._addLeadingZero(Math.floor(self.diffTimeMilsc() / dayInMilsc));
                    });
                }
            });
            return this
        },

        initialize: function() {
            var self = this;
            this._super();
            var currentTime = luxon.DateTime.local().setZone(self.config_time_zone);
            self.currentToDate = luxon.DateTime.fromSQL(self.date_to).setZone(self.config_time_zone);
            var diff = self.currentToDate.diff(currentTime).toObject();
            self.diffTimeMilsc(diff.milliseconds);
            self._startCountdown();
        },

        _startCountdown: function() {
            var self = this;

            var interval = 1000;
            self.activeTimer = setInterval(function() {
                var newDiff = self.diffTimeMilsc() - interval;
                if (newDiff <= 0) {
                    self.diffTimeMilsc(0);
                    clearInterval(self.activeTimer);
                } else {
                    self.diffTimeMilsc(newDiff);
                }
            }, interval);
        },

        _addLeadingZero: function(value) {
            return ('0' + value).slice(-2);
        }
    });
});