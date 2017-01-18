
angular.module('contenta').factory('echoService', function() {
	return {
		echo: function(message) {
			alert(message);
		}
	}
});
