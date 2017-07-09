(function(angular, $, _) {

  // Cache list of actions
  var actions = [];

  angular.module('api4').config(function($routeProvider) {
      $routeProvider.when('/api4/:entity?/:action?', {
        controller: 'Api4Explorer',
        templateUrl: '~/api4/Explorer.html',
        reloadOnSearch: false
      });
    }
  );

  angular.module('api4').controller('Api4Explorer', function($scope, $routeParams, $location, crmStatus, crmUiHelp, crmApi4) {
    var ts = $scope.ts = CRM.ts('api4');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/Api4/Explorer'});
    $scope.entities = CRM.vars.api4.entities;
    $scope.actions = actions;

    $scope.entity = $routeParams.entity;
    $scope.result = [];

    function writeCode() {

    }

    $scope.execute = function() {
      crmApi4($scope.entity, $scope.action, $scope.params)
        .then(function(data) {
          var meta = {},
            result = JSON.stringify(data, null, 2);
          data.length = 0;
          _.assign(meta, data);
          $scope.result = [JSON.stringify(meta).replace('{', '').replace(/}$/, ''), result];
        });
    };

    if (!$scope.entity) {
      $scope.helpTitle = ts('Help');
      $scope.helpText = [ts('Welcome to the api explorer.'), ts('Select an entity to begin.')];
    } else if (!actions.length) {
      crmApi4($scope.entity, 'getActions')
        .then(function(data) {
          _.each(data, function(action) {
            action.id = action.text = action.name;
            delete(action.name);
            actions.push(action);
            $scope.action = $routeParams.action;
          });
        });
    } else {
      $scope.action = $routeParams.action;
    }

    if ($scope.entity) {
      $scope.helpTitle = $scope.entity;
      $scope.helpText = [ts('Select an action')];
    }

    // Update route when changing entity
    $scope.$watch('entity', function(newVal, oldVal) {
      if (oldVal !== newVal) {
        // Flush actions cache to re-fetch for new entity
        actions = [];
        $location.url('/api4/' + newVal);
      }
    });

    // Update route when changing actions
    $scope.$watch('action', function(newVal, oldVal) {
      if ($scope.entity && $routeParams.action !== newVal && !_.isUndefined(newVal)) {
        $location.url('/api4/' + $scope.entity + '/' + newVal);
      } else if (newVal) {
        $scope.helpTitle = $scope.entity + '::' + newVal;
        $scope.helpText = [_.findWhere(actions, {id: newVal}).description];
      }
    });

  });

})(angular, CRM.$, CRM._);