angular.module('visualisasi')
.controller('mainController', ['$scope', '$http', '$interval', function($scope, $http, $interval) {
	var delay = 1000;
	$scope.currTime = 0;
	$scope.submitted = false;
	$scope.submitRating = function(isPositive) {
		var req = {
			method: 'POST',
			url: '/visualization/rating',
			data: { isPositive: isPositive }
		};

		$http(req)
			.then(function(response) {
				console.log(response.data);
		  		$scope.submitted = true;
		  	}, function(response) {

		  	});
	}

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
		    case 'Bar Chart': // bar chart
		    	baseConfiguration.data.type = 'bar';
		        break;
		    case 'Histogram': // histogram

		        break;
		    case 'Area Size Chart': // area size
		    
		        break;
		    case 'Pie Chart': // pie chart
		    	baseConfiguration.data.type = 'pie';
		    	baseConfiguration.data.rows = null;
		    	baseConfiguration.data.x = null;
		    	var columndata = [];
		    	for (var i = 1; i < conf.data.length; i++) {
		    		columndata.push(conf.data[i]);
		    	};
		    	console.log(columndata);
		    	baseConfiguration.data.columns = columndata;
		        break;
		    case 'Stacked Bar Chart': // stacked bar
		    	baseConfiguration.data.type = 'bar';
		    	var groups = [];
		    	for (var i = 0; i < conf.data[0].length; i++) {
		    		if (conf.data[0][i] != conf.category)
		    			groups.push(conf.data[0][i]);
		    	};
		    	baseConfiguration.data.groups = [groups];
		        break;
		    case 'Line Chart': // line	
		    	// default is line, do nothing
		        break;
		    case 'Area Chart': // area
		    	baseConfiguration.data.type = 'area';		    
		        break;
		    case 'Stacked Area Chart': // stacked area
		    	baseConfiguration.data.type = 'area';
		    	var groups = [];
		    	for (var i = 0; i < conf.data[0].length; i++) {
		    		if (conf.data[0][i] != conf.category)
		    			groups.push(conf.data[0][i]);
		    	};
		    	baseConfiguration.data.groups = [groups];		
		        break;
		    case 'Scatter Plot': // scatter
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
		    case 'Bubble Plot': // bubble
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
		    	baseConfiguration.point = {
		    		r: function(d) {
		    			console.log(d);
		    			return d.value;
		    		}
		    	};
		        break;
		}
		$scope.chart = c3.generate(baseConfiguration);
	}

	$http.get('/visualization/data')
	.then(function(response) {
  		console.log(response.data);
    	$scope.loadChart(response.data);
    	$interval(function() {
    		$scope.currTime += 1;
    	}, delay);
  	}, function(response) {

  	});
}]);