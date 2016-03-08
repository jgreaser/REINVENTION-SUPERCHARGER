angular.module('app.controllers', [])

.controller('homeCtrl', function($scope, $http) {
    var vm = this;

    vm.trelloEmail = 'joegreaser+4zs8ycbrixswmrveqhjq@boards.trello.com';

    vm.title = "";
    vm.inspiration = "";
    vm.problem = "";
    vm.solution = "";
    vm.contact = "";
    vm.category = "";

    vm.isStarted = false;
    vm.submitted = false;

    vm.titleIsSet = false;
    vm.categoryIsSet = false;
    vm.inspirationIsSet = false;
    vm.problemIsSet = false;
    vm.solutionIsSet = false;
    vm.contactIsSet = false;

    vm.titleIsVisible = false;
    vm.categoryIsVisible = false;
    vm.inspirationIsVisible = false;
    vm.problemIsVisible = false;
    vm.solutionIsVisible = false;
    vm.contactIsVisible = false;

    vm.isNotStarted = function() {
        if (vm.isStarted == false) {
            return true;
        } else {
            return false;
        }
    }

    vm.getStarted = function() {

        vm.isStarted = true;
    }



    vm.startIsReady = function() {
        if (vm.titleIsSet == false && vm.inspirationIsSet == false && vm.problemIsSet == false && vm.solutionIsSet == false && vm.contactIsSet == false && vm.categoryIsSet == false) {
            return true;
        }
    };

    vm.submitIsReady = function() {
        if (vm.submitted == false && vm.titleIsSet == true && vm.inspirationIsSet == true && vm.problemIsSet == true && vm.solutionIsSet == true && vm.contactIsSet == true && vm.categoryIsSet == true) {
            return true;
        }
    };

    vm.submittedIsTrue = function() {

        if (vm.submitted == true && vm.titleIsSet == true && vm.inspirationIsSet == true && vm.problemIsSet == true && vm.solutionIsSet == true && vm.contactIsSet == true && vm.categoryIsSet == true) {
            return true;
        }
    };

    vm.submitIdea = function() {
        vm.submitted = true;
        //vm.startOver();
        vm.logResponse();
    }

    vm.startOver = function() {


        vm.title = "";
        vm.inspiration = "";
        vm.problem = "";
        vm.solution = "";
        vm.contact = "";
        vm.category = "";

        vm.isStarted = false;
        vm.submitted = false;

        vm.titleIsSet = false;
        vm.categoryIsSet = false;
        vm.inspirationIsSet = false;
        vm.problemIsSet = false;
        vm.solutionIsSet = false;
        vm.contactIsSet = false;

        vm.titleIsVisible = false;
        vm.categoryIsVisible = false;
        vm.inspirationIsVisible = false;
        vm.problemIsVisible = false;
        vm.solutionIsVisible = false;
        vm.contactIsVisible = false;



    };
    /*vm.isSet = function(param){
		if (vm.category != param){return true;}

	};
*/


    this.logResponse = function() {


        $http.get("https://reinvention.flvs.net/supercharger/php/mailto.php?&receiver=" + vm.trelloEmail + "&title=" + vm.title + "&inspiration=" + vm.inspiration + "&problem=" + vm.problem + "&solution=" + vm.solution + "&category=" + vm.category + "&contact=" + vm.contact)
            .then(function(response) {
                //$http.get("https://reinvention.flvs.net/supercharger/php/mailto.php?&receiver="+'jgreaser@gmail.com'+"&title=somethingsubmitted&description=success");

            });
    };


})

.controller('thanksCtrl', function($scope) {

})