angular.module('visualisasi')
.controller('mainController', ['$scope', '$http', function($scope, $http) {
	$scope.loadChart = function(conf) {
		var baseConfiguration = {
			data: {
				x: conf.category,
				rows: conf.data
			},
			axis: {
			    x: {
			        type: 'category'
			    }
			}
		};
		// get selected visualization
		switch(conf.visualization) {
		    case 1: // bar chart
		    	baseConfiguration.data.type = 'bar';
		        break;
		    case 2: // histogram

		        break;
		    case 3: // area size
		    
		        break;
		    case 4: // pie chart
		    	baseConfiguration.data.type = 'pie';
		        break;
		    case 5: // stacked bar
		    	baseConfiguration.data.type = 'bar';
		    	var groups = [];
		    	for (var i = 1; i < conf.data[0].length; i++) {
		    		groups.push(conf.data[0][i]);
		    	};
		    	baseConfiguration.data.groups = [groups];
		        break;
		    case 6: // line	
		    	// default is line, do nothing
		        break;
		    case 7: // area
		    	baseConfiguration.data.type = 'area';		    
		        break;
		    case 8: // stacked area
		    	baseConfiguration.data.type = 'area';
		    	var groups = [];
		    	for (var i = 1; i < conf.data[0].length; i++) {
		    		groups.push(conf.data[0][i]);
		    	};
		    	baseConfiguration.data.groups = [groups];		
		        break;
		    case 9: // scatter
		    	baseConfiguration.data.type = 'scatter';
		    	baseConfiguration.data.x = conf.data[0][0]; // get first column
		    	baseConfiguration.axis = {
		    		x: {
		    			label: conf.data[0][0]
		    		},
		    		y: {
		    			label: conf.data[0][1]
		    		}
		    	};
		        break;
		    case 10: // bubble
		    
		        break;
		}
		$scope.chart = c3.generate(baseConfiguration);
	}
	$http.get('integration/data')
	.then(function(response) {
  		console.log(response.data);
    	$scope.loadChart(response.data);
  	}, function(response) {

  	});
}]);