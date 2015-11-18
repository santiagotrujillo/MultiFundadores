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
    $router
    .when("/propietario", {
        templateUrl: "/templates/propietarios/list.html"
    })
    .when("/recaudos", {
        templateUrl: "/templates/recaudos/index.html"
    })
    .when("/operaciones", {
        templateUrl: "/templates/operaciones/index.html"
    })
    .when("/egresos", {
        templateUrl: "/templates/egresos/index.html"
    })
    .otherwise({
        redirectTo: '/operaciones'
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
            alert('Se eliminï¿½ el Propietario');
            window.location.reload();
        });
    };

    $scope.editar= function(id)
    {
        $http.get('/propietarios/editar/'+$scope.propietarioEditar.id)
            .success(function(data, status, headers, config)
        {
            alert('Se editï¿½ el Propietario');
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

app.controller("RecaudoController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.propietarios = [], $scope.total=0, $scope.propietarioEditar= {}, $scope.propietarioBorrar ={};

        $scope.listar = function(page)
        {
            $http.get('/propietarios/cobro/admin/pendientes')
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

        $scope.cargarPago = function(propietario)
        {
            $scope.propietarioEditar = propietario;
            $scope.propietario = {};

            $http.get('/propietarios/show/'+$scope.propietarioEditar.id)
                .success(function(data, status, headers, config)
                {
                    $scope.propietario = data;
                });
        };

        $scope.realizarPago = function()
        {
            $http.post('/propietarios/pagar')
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

app.controller("OperacionesController", ['$scope', '$http', function($scope, $http)
{
    $scope.realizarCobroAdmin = function ()
    {
        closeModal('cobrosAdmin');
        $http.post('/propietarios/cobro/admin')
            .success(function(data, status, headers, config)
            {
                alert('Se cargaron los cobros de adminsitración correctamente')
            })
            .error(function(error, status, headers, config)
            {
                alert('Hubo un error')
            });
    }
}]);

app.controller("EgresosController", ['$scope', '$http', function($scope, $http)
{
    $scope.realizarCobroAdmin = function ()
    {
        closeModal('cobrosAdmin');
        $http.post('/propietarios/cobro/admin')
            .success(function(data, status, headers, config)
            {
                alert('Se cargaron los cobros de adminsitración correctamente')
            })
            .error(function(error, status, headers, config)
            {
                alert('Hubo un error')
            });
    };
}]);