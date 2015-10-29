var app = angular.module("multifundadores", ["ngTable"]);

app.service('TableService', function ($http, $filter) {

    function filterData(data, filter){
        return $filter('filter')(data, filter)
    }

    function orderData(data, params){
        return params.sorting() ? $filter('orderBy')(data, params.orderBy()) : filteredData;
    }

    function sliceData(data, params){
        return data.slice((params.page() - 1) * params.count(), params.page() * params.count())
    }

    function transformData(data,filter,params){
        return sliceData( orderData( filterData(data,filter), params ), params);
    }
    var service = {
        cachedData:[],
        getTable:function($defer, params, filter, data){

            if(service.cachedData.length>0){
                service.cachedData = data;
                var filteredData = filterData(service.cachedData,filter);
                var transformedData = sliceData(orderData(filteredData,params),params);
                params.total(filteredData.length)
                $defer.resolve(transformedData);
            }
            else
            {
                angular.copy(data,service.cachedData)
                params.total(data.length)
                var filteredData = $filter('filter')(data, filter);
                var transformedData = transformData(data,filter,params)
                $defer.resolve(transformedData);
            }
        }
    };
    return service;

});

app.config(["$routeProvider", function($router)
{
    $router.when("/propietario", {
        templateUrl: "/templates/propietarios/list.html",
    }).when("/list", {
        templateUrl: "./templates/list.html",
        controller: "ListController"
    }).otherwise({
        redirectTo: '/'
    });
}]);

app.controller("PropietarioController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
{
    $scope.propietarios = [], $scope.total=0, $scope.propietarioEditar= {}, $scope.propietarioBorrar ={};

    $scope.listar = function(page)
    {
        $http.get('/propietarios/listar')
            .success(function(data, status, headers, config)
            {
                $scope.propietarios = $scope.propietarios.concat(data);
                $scope.total=$scope.propietarios.length;
                $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { id: 'asc'}}, {
                    total: $scope.propietarios.length,
                    getData: function($defer, params)
                    {
                        TableService.getTable($defer,params,$scope.filter, $scope.propietarios);
                    }
                });
                $scope.tableParams.reload();
                $scope.$watch("filter.$", function () {
                    $scope.tableParams.reload();
                });
            });
    };
    $scope.listar();

    $scope.eliminar= function()
    {
        $http.get('/propietarios/borrar/'+$scope.propietarioBorrar.id)
        .success(function(data, status, headers, config)
        {
            alert('Se eliminó el Propietario');
            window.location.reload();
        });
    };

    $scope.editar= function(id)
    {
        $http.get('/propietarios/editar/'+$scope.propietarioEditar.id)
            .success(function(data, status, headers, config)
        {
            alert('Se editó el Propietario');
            window.location.reload();
        });
    };

    $scope.showEdit = function(propietario)
    {
        $scope.propietarioEditar = propietario;
        $scope.propietario = {};

        $http.get('/propietarios/show/'+$scope.propietarioEditar.id)
            .success(function(data, status, headers, config)
            {
                $scope.propietario = data;
            });
    };

    $scope.showDelete = function(propietario)
    {
        $scope.propietarioBorrar = propietario;
    };
    $scope.actualizar = function()
    {
        $http.post('/propietarios/update')
            .success(function(data, status, headers, config)
            {
                console.log('success', data);
            })
            .error(function(error, status, headers, config)
            {
                console.log('error', error);
            });
    }
}]);


