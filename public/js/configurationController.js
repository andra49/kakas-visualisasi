angular.module('visualisasi')
.controller('configurationController', ['$scope', '$http', function($scope, $http) {
	$scope.recommendations = [];
	$scope.selection = [];
	$scope.isExact = true;
	$scope.isAggregate = false;
	$scope.purpose = "ALL";

	$scope.loadRecommendation = function() {
		var req = {
			method: 'POST',
			url: 'recommendation',
			data: { selection: $scope.selection,
					aggregate: $scope.isAggregate,
					exact: $scope.isExact,
					purpose: $scope.purpose }
		};

		$http(req)
			.then(function(response) {
				console.log(response.data);
		  		$scope.recommendations = response.data.mappings;
		  	}, function(response) {

		  	});
	};

	$scope.toggle = function toggleSelection(attribute) {
	    var idx = $scope.selection.indexOf(attribute);

	    // is currently selected
	    if (idx > -1) {
	      $scope.selection.splice(idx, 1);
	    }

	    // is newly selected
	    else {
	      $scope.selection.push(attribute);
	    }
	    console.log($scope.selection);
	};
}]);