angular.module('mediaModule', [
	'ngRoute',
	'contenta.media.controllers',
	'contenta.media.directives'
]);

var mod = angular.module('mediaModule');

var seriesHome = mod.component('series-home', {
	templateUrl: webRoot + '/public/app/media/series-home.template.html',
	controller: 'SeriesHomeController'
});

var seriesList = mod.component('series-list', {
	templateUrl: webRoot + '/public/app/media/series-list.template.html',
	controller: 'SeriesListController'
});
