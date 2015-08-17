angular.module('visualisasi')
.controller('mainController', ['$scope', '$http', function($scope, $http) {
	$scope.loadBarchart = function(data, category) {
		$scope.chart = c3.generate({ 
			data: {
				x: category,
				rows: data,
				type: 'bar'
			},
			axis: {
			    x: {
			        type: 'category' // this needed to load string x value
			    }
			}
		});
	}
	$http.get('integration/data').
  	then(function(response) {
  		console.log(response.data);
    	$scope.loadBarchart(response.data.data, response.data.category);
  	}, function(response) {

  	});
}]);