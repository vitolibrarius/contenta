'use strict';

angular.module('contenta', [
	'ngRoute',
	'loginModule',
	'menuModule',
	'mediaModule'
]);

angular.module('contenta').config([
	'$locationProvider',
	'$routeProvider',
	function config($locationProvider, $routeProvider) {
		$locationProvider.hashPrefix('!');
// 		$locationProvider.html5Mode(true);
		$routeProvider.
			when('/menu', {
				template: '<menu></menu>'
			}).
			when('/series', {
				template: '<series-home></series-home>'
			}).
			when('/publication', {
				template: '<publication-list></publication-list>'
			}).
			when('/publisher', {
				template: '<publisher-list></publisher-list>'
			}).
			otherwise('/series');
	}
]);

