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
    .when("/menu/ingresos", {
        templateUrl: "/templates/ingresos/menu.html"
    })
    .when("/ingresos/efectivo", {
        templateUrl: "/templates/ingresos/efectivo.html"
    })
    .when("/ingresos/consignaciones", {
        templateUrl: "/templates/ingresos/consignaciones.html"
    })
    .when("/ingresos", {
        templateUrl: "/templates/ingresos/reporte_total.html"
    })
    .when("/pagos", {
        templateUrl: "/templates/pagos/index.html"
    })
    .when("/pago/profile/:id", {
        templateUrl: "/templates/pagos/profile.html"
    })
    .when("/confirmacion/abono/:id", {
        templateUrl: "/templates/abonos/profile.html"
    })
    .when("/egresos/profile/:id", {
        templateUrl: "/templates/egresos/profile.html"
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
        $scope.propietarios = [], $scope.total=0, $scope.propietarioPago= {}, $scope.pagorealizado = {};

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
            $scope.propietarioPago = propietario;
            $scope.propietario = {};
        };

        $scope.realizarPago = function()
        {
            $http({
                method: 'POST',
                url: '/propietarios/abonar',
                data: $scope.propietarioPago,
            })
            .success(function(data, status, headers, config)
            {
                console.log('data', data);
                $scope.pagorealizado= data;

                $scope.cerrarModalPago();

                $scope.abrirModalCargoAbono();
            })
            .error(function(error, status, headers, config)
            {
                console.log('error', error)
                alert(error["message"])
                window.location.reload();
            });
        };

        $scope.deshacerAbono = function()
        {
            $scope.deshacerPago = {pago_id : $scope.pagorealizado.factura.id, abono_id : $scope.pagorealizado.abono.id };

            $http({
                method: 'POST',
                url: '/propietarios/deshacer/abono',
                data: $scope.deshacerPago,
            })
            .success(function(data, status, headers, config)
            {
                alert("El abono fue deshecho, no se realizo el pago");
                console.log('data', data);
            })
            .error(function(error, status, headers, config)
            {
                console.log('error', error)
                alert(error["message"])
                window.location.reload();
            });
        };

        $scope.verConfirmacion = function()
        {
            verConfirmacion();
        };

        $scope.cerrarModalPago = function()
        {
            cerrarModalPago()
        };

        $scope.abrirModalCargoAbono = function()
        {
            abrirModalCargoAbono()
        };
    }]);

app.controller("OperacionesController", ['$scope', '$http', function($scope, $http)
{
    $scope.realizarCobroAdmin = function ()
    {
        closeModal('cobrosAdmin');
        $http.post('/propietarios/cobro/admin')
        .success(function(data, status, headers, config)
        {
            alert('Se cargaron los cobros de administración correctamente')
        })
        .error(function(error, status, headers, config)
        {
            alert(error["message"])
        });
    };

    $scope.realizarCobroSeguro = function ()
    {
        closeModal('cobrosSeguro');
        $http.post('/propietarios/cobro/seguro')
        .success(function(data, status, headers, config)
        {
            alert('Se cargaron los cobros de seguro correctamente')
        })
        .error(function(error, status, headers, config)
        {
            alert(error["message"])
        });
    };

    $scope.realizarCobroSalon = function()
    {
        closeModal('cobrosSalon');
        $http({
            method: 'POST',
            url: '/propietarios/cobro/salon',
            data: $scope.pago,
        })
        .success(function(data, status, headers, config)
        {
            alert('Se cargo el cobro del salon correctamente')
        })
        .error(function(error, status, headers, config)
        {
            alert(error["message"])
        });
    };

    $scope.realizarCobroParquedero = function()
    {
        closeModal('cobrosParqueadero');
        $http({
            method: 'POST',
            url: '/propietarios/cobro/parqueadero',
            data: $scope.pago,
        })
        .success(function(data, status, headers, config)
        {
            alert('Se cargo el cobro del parqueadero correctamente')
        })
        .error(function(error, status, headers, config)
        {
            alert(error["message"])
        });
    };

    $scope.realizarCobroCuentaCobro = function()
    {
        closeModal('cobrosCuentaCobrar');
        $http({
            method: 'POST',
            url: '/propietarios/cobro/cuentacobro',
            data: $scope.pago,
        })
            .success(function(data, status, headers, config)
            {
                alert('Se cargo el cobro del parqueadero correctamente')
            })
            .error(function(error, status, headers, config)
            {
                alert(error["message"])
            });
    }

    $scope.realizarCobroOtros = function()
    {
        closeModal('cobrosOtros');
        $http({
            method: 'POST',
            url: '/propietarios/cobro/otros',
            data: $scope.pago,
        })
            .success(function(data, status, headers, config)
            {
                alert('Se cargo el cobro correctamente')
            })
            .error(function(error, status, headers, config)
            {
                alert(error["message"])
            });
    }
}]);

app.controller("EgresosController", ['$scope', '$http', function($scope, $http)
{
    $scope.tipo_deudas = []; $scope.egreso = null;
    $scope.listarTipoDeudas = function()
    {
        $http.get('/usuarios/tipodeudas')
        .success(function(data, status, headers, config)
        {
            $scope.tipo_deudas = data;
        })
    };
    $scope.listarTipoDeudas();

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

    $scope.enviarEgreso = function()
    {
        abrirModal('cargoEgreso');
    }

    $scope.realizarEgreso = function()
    {
        $http({
            method: 'POST',
            url: '/usuarios/egreso',
            data: $scope.egreso,
        })
        .success(function(data, status, headers, config)
        {
            cerrarModal('cargoEgreso');
            alert('Se realizo el egreso');
            console.log('dataxx', data);
            window.location.href= '#/egresos/profile/'+data.id;
        })
        .error(function(error, status, headers, config)
        {
            alert(error);
        });
    }
}]);

app.controller("PagosController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.pagos = [], $scope.total=0;

        $scope.listar = function(page)
        {
            $http.get('/propietarios/pagos/relizados')
                .success(function(data, status, headers, config)
                {
                    $scope.pagos = $scope.pagos.concat(data);
                    $scope.total=$scope.pagos.length;
                    $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { id: 'asc'}}, {
                        total: $scope.pagos.length,
                        getData: function($defer, params)
                        {
                            TableService.getTable($defer,params,$scope.filter, $scope.pagos);
                        }
                    });
                    $scope.tableParams.reload();
                    $scope.$watch("filter.$", function () {
                        $scope.tableParams.reload();
                    });
                });
        };
        $scope.listar();
    }]);

app.controller("PagoProfile",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams', function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params)
{
    $scope.abonos = [], $scope.total=0;
    $scope.listar = function(page)
    {
        $http.get('/propietarios/abonos/pago/'+$params.id)
            .success(function(data, status, headers, config)
            {
                $scope.abonos = $scope.abonos.concat(data);
                $scope.total=$scope.abonos.length;
                $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { id: 'asc'}}, {
                    total: $scope.abonos.length,
                    getData: function($defer, params)
                    {
                        TableService.getTable($defer,params,$scope.filter, $scope.abonos);
                    }
                });
                $scope.tableParams.reload();
                $scope.$watch("filter.$", function () {
                    $scope.tableParams.reload();
                });
            });
    };
    $scope.listar();
}]);

app.controller("EgresoProfile",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams', function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params)
{
    $scope.deuda = {};
    $scope.show = function(page)
    {
        $http.get('/usuarios/egreso/'+$params.id)
            .success(function(data, status, headers, config)
            {
               $scope.deuda =data;
            });
    };
    $scope.show();

    $scope.imprimir = function()
    {
        window.print();
    }
}]);

app.controller("IngresosController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.ingresos = [], $scope.fecha_inicial= null, $scope.fecha_final = null, $scope.matrizIngresos= [];

        $scope.total = { admin :0, seguro :0, salon:0, incumplimiento: 0, parqueadero:0, otros:0, total:0};

$scope.obtenerReporte = function()
        {
            $http.get('/usuarios/ingresos/totales/'+$scope.fecha_inicial+'/'+$scope.fecha_final)
                .success(function(data, status, headers, config)
                {
                    $scope.ingresos = data;
                    $scope.matrizIngresos =[];
                    $scope.total = { admin :0, seguro :0, salon:0, incumplimiento: 0, parqueadero:0, otros:0 ,total:0};

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        if($scope.ValidateMatriz(ingreso.prefix) == -1)
                        {
                            $scope.matrizIngresos.push({ prefix : ingreso.prefix, admin : 0,
                                                          seguro:0, salon:0, incumplimiento :0, otros : 0, parqueadero:0 , periodo: ingreso.month +"-"+ingreso.year, total:0})
                        }
                    });

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        index = $scope.ValidateMatriz(ingreso.prefix);
                        if(ingreso.id == 1)
                        {
                            $scope.total.admin = $scope.total.admin+ ingreso.ingresos;
                            $scope.matrizIngresos[index].admin = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 2)
                        {
                            $scope.total.seguro = $scope.total.seguro+ ingreso.ingresos;
                            $scope.matrizIngresos[index].seguro = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 3)
                        {
                            $scope.total.salon = $scope.total.salon+ ingreso.ingresos;
                            $scope.matrizIngresos[index].salon = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 4)
                        {
                            $scope.total.incumplimiento = $scope.total.incumplimiento+ ingreso.ingresos;
                            $scope.matrizIngresos[index].incumplimiento = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 5)
                        {
                            $scope.total.parqueadero = $scope.total.parqueadero+ ingreso.ingresos;
                            $scope.matrizIngresos[index].parqueadero = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 6)
                        {
                            $scope.total.otros = $scope.total.otros+ ingreso.ingresos;
                            $scope.matrizIngresos[index].otros = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                    });
                    $scope.total.total = $scope.total.admin + $scope.total.seguro+$scope.total.salon +$scope.total.incumplimiento+$scope.total.parqueadero+$scope.total.otros;
                    console.log('matriz', $scope.matrizIngresos);
                });
        };

        $scope.imprimir = function()
        {
            window.print();
        }

        $scope.ValidateMatriz = function(prefix)
        {
            var posicion= -1;
            $scope.matrizIngresos.forEach(function(ingreso, index)
            {
                if(ingreso.prefix == prefix)
                {
                    posicion= index;
                }
            });
            return posicion;
        };
    }]);

app.controller("IngresosEfectivoController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.ingresos = [], $scope.fecha_inicial= null, $scope.fecha_final = null, $scope.matrizIngresos= [];

        $scope.total = { admin :0, seguro :0, salon:0, incumplimiento: 0, parqueadero:0, otros:0, total:0};

        $scope.obtenerReporte = function()
        {
            $http.get('/usuarios/ingresos/efectivos/totales/'+$scope.fecha_inicial+'/'+$scope.fecha_final)
                .success(function(data, status, headers, config)
                {
                    $scope.ingresos = data;
                    $scope.matrizIngresos =[];
                    $scope.total = { admin :0, seguro :0, salon:0, incumplimiento: 0, parqueadero:0, otros:0 ,total:0};

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        if($scope.ValidateMatriz(ingreso.prefix) == -1)
                        {
                            $scope.matrizIngresos.push({ prefix : ingreso.prefix, admin : 0,
                                seguro:0, salon:0, incumplimiento :0, otros : 0, parqueadero:0 , periodo: ingreso.month +"-"+ingreso.year, total:0})
                        }
                    });

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        index = $scope.ValidateMatriz(ingreso.prefix);
                        if(ingreso.id == 1)
                        {
                            $scope.total.admin = $scope.total.admin+ ingreso.ingresos;
                            $scope.matrizIngresos[index].admin = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 2)
                        {
                            $scope.total.seguro = $scope.total.seguro+ ingreso.ingresos;
                            $scope.matrizIngresos[index].seguro = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 3)
                        {
                            $scope.total.salon = $scope.total.salon+ ingreso.ingresos;
                            $scope.matrizIngresos[index].salon = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 4)
                        {
                            $scope.total.incumplimiento = $scope.total.incumplimiento+ ingreso.ingresos;
                            $scope.matrizIngresos[index].incumplimiento = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 5)
                        {
                            $scope.total.parqueadero = $scope.total.parqueadero+ ingreso.ingresos;
                            $scope.matrizIngresos[index].parqueadero = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 6)
                        {
                            $scope.total.otros = $scope.total.otros+ ingreso.ingresos;
                            $scope.matrizIngresos[index].otros = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                    });
                    $scope.total.total = $scope.total.admin + $scope.total.seguro+$scope.total.salon +$scope.total.incumplimiento+$scope.total.parqueadero+$scope.total.otros;
                    console.log('matriz', $scope.matrizIngresos);
                });
        };

        $scope.imprimir = function()
        {
            window.print();
        }

        $scope.ValidateMatriz = function(prefix)
        {
            var posicion= -1;
            $scope.matrizIngresos.forEach(function(ingreso, index)
            {
                if(ingreso.prefix == prefix)
                {
                    posicion= index;
                }
            });
            return posicion;
        };
    }]);

app.controller("IngresosConsignacionController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.ingresos = [], $scope.fecha_inicial= null, $scope.fecha_final = null, $scope.matrizIngresos= [];

        $scope.total = { admin :0, seguro :0, salon:0, incumplimiento: 0, parqueadero:0, otros:0, total:0};

        $scope.obtenerReporte = function()
        {
            $http.get('/usuarios/ingresos/consignaciones/totales/'+$scope.fecha_inicial+'/'+$scope.fecha_final)
                .success(function(data, status, headers, config)
                {
                    $scope.ingresos = data;
                    $scope.matrizIngresos =[];
                    $scope.total = { admin :0, seguro :0, salon:0, incumplimiento: 0, parqueadero:0, otros:0 ,total:0};

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        if($scope.ValidateMatriz(ingreso.prefix) == -1)
                        {
                            $scope.matrizIngresos.push({ prefix : ingreso.prefix, admin : 0,
                                seguro:0, salon:0, incumplimiento :0, otros : 0, parqueadero:0 , periodo: ingreso.month +"-"+ingreso.year, total:0})
                        }
                    });

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        index = $scope.ValidateMatriz(ingreso.prefix);
                        if(ingreso.id == 1)
                        {
                            $scope.total.admin = $scope.total.admin+ ingreso.ingresos;
                            $scope.matrizIngresos[index].admin = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 2)
                        {
                            $scope.total.seguro = $scope.total.seguro+ ingreso.ingresos;
                            $scope.matrizIngresos[index].seguro = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 3)
                        {
                            $scope.total.salon = $scope.total.salon+ ingreso.ingresos;
                            $scope.matrizIngresos[index].salon = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 4)
                        {
                            $scope.total.incumplimiento = $scope.total.incumplimiento+ ingreso.ingresos;
                            $scope.matrizIngresos[index].incumplimiento = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 5)
                        {
                            $scope.total.parqueadero = $scope.total.parqueadero+ ingreso.ingresos;
                            $scope.matrizIngresos[index].parqueadero = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                        else if(ingreso.id == 6)
                        {
                            $scope.total.otros = $scope.total.otros+ ingreso.ingresos;
                            $scope.matrizIngresos[index].otros = ingreso.ingresos;
                            $scope.matrizIngresos[index].total = $scope.matrizIngresos[index].total+ ingreso.ingresos;
                        }
                    });
                    $scope.total.total = $scope.total.admin + $scope.total.seguro+$scope.total.salon +$scope.total.incumplimiento+$scope.total.parqueadero+$scope.total.otros;
                    console.log('matriz', $scope.matrizIngresos);
                });
        };

        $scope.imprimir = function()
        {
            window.print();
        }

        $scope.ValidateMatriz = function(prefix)
        {
            var posicion= -1;
            $scope.matrizIngresos.forEach(function(ingreso, index)
            {
                if(ingreso.prefix == prefix)
                {
                    posicion= index;
                }
            });
            return posicion;
        };
    }]);

app.controller("AbonoProfile",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams', function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params)
{
    $scope.abono = {};
    $scope.ver = function()
    {
        $http.get('/propietarios/abono/'+$params.id)
            .success(function(data, status, headers, config)
            {
                $scope.abono = data;
            });
    };
    $scope.ver();

    $scope.imprimir = function()
    {
        window.print();
    }
}]);


function cerrarModalPago()
{
    $('#pagoPropietario').modal('hide');
}

function abrirModalCargoAbono()
{
    $('#cargoAbono').modal('show');
}

function verConfirmacion()
{
    $('#cargoAbono').modal('hide');
    alert("Se realizó el pago satisfactoriamente")
    window.location.reload();
}

function abrirModal(name)
{
    $('#'+name).modal('show');
}

function cerrarModal(name)
{
    $('#'+name).modal('hide');
}
