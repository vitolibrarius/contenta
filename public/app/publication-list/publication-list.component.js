var mod = angular.module('myApp');
var component = mod.component('peopleList', {
		templateUrl: webRoot + '/public/app/people-list/people-list.template.html',
		controller: ['$http',
			function PhoneListController($http) {
				var self = this;
				self.orderProp = 'first_name';
				$http.get('Api/Person').then(function(response) {
					self.people = response.data['items'];
				});
			}
		]
	}
);
