angular.module('menuModule', [
	'contenta.menu.controllers',
	'contenta.menu.directives'
]);

var mod = angular.module('menuModule');
var component = mod.component('menu', {
	templateUrl: webRoot + '/public/app/menu/menu.template.html',
	controller: 'MenuController'
});
