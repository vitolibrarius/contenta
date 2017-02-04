<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $this->publication->displayName(); ?></title>
	<meta name="description" content="<?php echo $this->publication->displayName(); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
	if ( isset($this->additionalStyles)) {
		foreach ($this->additionalStyles as $key => $css) {
			echo '	<link rel="stylesheet" href="' . Config::Web($css) . '" />';
		}
	}
	if ( isset($this->additionalScripts)) {
		foreach ($this->additionalScripts as $key => $script) {
			echo '	<script type="text/javascript" src="' . Config::Web($script) . '"></script>' . PHP_EOL;
		}
	}
?>
<script type="text/javascript">
webRoot="<?php echo Config::Web(); ?>";
var sliderApp=angular.module('sliderApp',['ngAnimate']);

sliderApp.controller('SliderController', function($scope) {
    $scope.images=[
<?php foreach ($this->fileWrapper->imageContents() as $idx => $name ) {
		echo "	{src:'". Config::Web($this->imgRoot)."?media=".$this->media->pkValue()."&name=".urlencode($name)."', title:'".urlencode(basename($name))."'},".PHP_EOL;
	}
?>
    ];
});

sliderApp.directive('slider', function ($timeout) {
  return {
    restrict: 'AE',
	replace: true,
	scope:{
		images: '='
	},
    link: function (scope, elem, attrs) {
		scope.currentIndex=0;

		scope.next = function(){
			scope.currentIndex < scope.images.length-1 ? scope.currentIndex++ : scope.currentIndex=0;
		};

		scope.prev = function(){
			scope.currentIndex > 0 ? scope.currentIndex-- : scope.currentIndex = scope.images.length-1;
		};

		scope.$watch('currentIndex', function(){
			scope.images.forEach(function(image){
				image.visible=false;
			});
			scope.images[scope.currentIndex].visible=true;
		});

// 		Start: For Automatic slideshow*/
//
// 		var timer;
//
// 		var sliderFunc=function(){
// 			timer=$timeout(function(){
// 				scope.next();
// 				timer=$timeout(sliderFunc,5000);
// 			},5000);
// 		};
//
// 		sliderFunc();
//
// 		scope.$on('$destroy',function(){
// 			$timeout.cancel(timer);
// 		});
//
// 		End : For Automatic slideshow*/
    },
	template:'<div class="slider"><div class="slide" ng-repeat="image in images" ng-show="image.visible"><img ng-src="{{image.src}}"/></div><div class="arrows"><a href="#" ng-click="prev()"><span class="button" id="leftArrow"></span></a> <a href="#" ng-click="next()"><span class="button" id="rightArrow"></span></a></div></div>'
  }
});
	</script>

</head>
<body ng-app="sliderApp">
<section ng-controller="SliderController">
	<h1><?php echo $this->publication->name(); ?></h1>
	<div id="slideshow">
		<slider images="images"/>
	</div>
</section>
</body>
</html>
