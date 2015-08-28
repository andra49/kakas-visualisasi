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
			size: {
				height: 480
			},
			data: {
				x: conf.category,
				rows: conf.data
			},
			axis: {
				y : {
		            tick: {
		                format: d3.format("s")
		            }
		        },
			    x: {
			        type: 'category',
			        tick: {
			        	fit: false,
		                rotate: 60,
		                multiline: true
		            }
	            }
			},
			zoom: {
				enabled: false,
				rescale: true
			},
			subchart: {
				enabled: false
			}
		};

		// set interactive features
		var md = new MobileDetect(window.navigator.userAgent);
		var mobile = false;
		if (md.mobile())
			mobile = true;
		for (var i = 0; i < conf.activities.length; i++) {
			switch(conf.activities[i].name) {
				case 'zooming':
					baseConfiguration.zoom.enabled = true;
					break;
				case 'brushing':
					if (!mobile) {
						baseConfiguration.subchart.show = true;
					}
					break;
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
		    	baseConfiguration.axis = {
		    		x: {
		    			label: conf.data[0][1]
		    		},
		    		y: {
		    			label: conf.data[0][2]
		    		}
		    	};
		    	baseConfiguration.data.type = 'scatter';
		    	var data = [];
		    	var xs = {};
		    	for (var i = 1; i < conf.data.length; i++) {
		    		var category_x = conf.data[i][0]+"_x";
		    		var category = conf.data[i][0];
		    		data.push([category_x, conf.data[i][1]]);
		    		data.push([category, conf.data[i][2]]);
		    		xs[category] = category_x;
		    	};
		    	baseConfiguration.data.rows = null;
		    	baseConfiguration.data.columns = data;
		    	baseConfiguration.data.x = null;
		    	baseConfiguration.data.xs = xs;
		        break;
		    case 'Bubble Plot': // bubble
		    	baseConfiguration.axis = {
		    		x: {
		    			label: conf.data[0][1],
			        	tick: {
			        		fit: false
			        	}
		    		},
		    		y: {
		    			label: conf.data[0][2]
		    		}
		    	};
		    	baseConfiguration.data.type = 'scatter';
		    	var data = [];
		    	var xs = {};
		    	var values = {};
		    	for (var i = 1; i < conf.data.length; i++) {
		    		var category_x = conf.data[i][0]+"_x";
		    		var category = conf.data[i][0];
		    		var value = conf.data[i][3];
		    		data.push([category_x, conf.data[i][1]]);
		    		data.push([category, conf.data[i][2]]);
		    		values[category] = (value);
		    		xs[category] = category_x;
		    	};
		    	var maxValue = 0;
		    	for (var i = 0; i < data.length; i++) {
		    		if (maxValue < values[data[i][0]])
		    			maxValue = values[data[i][0]];
		    	};
		    	baseConfiguration.data.rows = null;
		    	baseConfiguration.data.columns = data;
		    	baseConfiguration.data.x = null;
		    	baseConfiguration.data.xs = xs;
		    	var maxBubbleSize = 50;
		    	baseConfiguration.point = {
		    		r:  function(d) {
			       		return (values[d.id]/maxValue) * maxBubbleSize;
			    	}
			    }
		        break;
		}
		console.log(JSON.stringify(baseConfiguration.data));
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