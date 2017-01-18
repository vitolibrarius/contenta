var mediaCtrl = angular.module('contenta.media.controllers', [
]);

mediaCtrl.controller('SeriesHomeController',function($scope, echoService) {
	$scope.message = "Series Home";
	echoService.echo("Hello World");
});

mediaCtrl.controller('SeriesListController',
	function SeriesListController($scope) {
		$scope.message = "Series Stuff";
		$scope.list = [
		{
			name: 'Nexus S',
			snippet: 'Fast just got faster with Nexus S.'
		},
		{
			name: 'Motorola XOOM™ with Wi-Fi',
			snippet: 'The Next, Next Generation tablet.'
		},
		{
			name: 'MOTOROLA XOOM™',
			snippet: 'The Next, Next Generation tablet.'
		}
	];
});
