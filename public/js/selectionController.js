angular.module('visualisasi')
.controller('selectionController', ['$scope', '$http', function($scope, $http) {
	$scope.disable = false;
	$scope.selection;

	$scope.toggleSelection = function() {
		var exist = false;
	    for (var i = 0; i < stringColumns.length; i++) {
	    	if ($scope.selection == stringColumns[i]){
	    		$scope.disable = true;
	    		exist = true;
	    	}
	    };
	    if (!exist)
	    	$scope.disable = false;
	};
}]);