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
    .when("/reporte_egresos", {
        templateUrl: "/templates/reporte_egresos/index.html"
    })
    .when("/reporte_egresos/:date1/:date2/:concept", {
        templateUrl: "/templates/reporte_egresos/detail.html"
    })
    .when("/ingresos-efectivo-detail/:periodo/:id", {
        templateUrl: "/templates/ingresos/efectivo_detail.html"
    })
    .when("/reporte-total-detail/:periodo/:id", {
        templateUrl: "/templates/ingresos/reporte_total_detail.html"
    })
    .when("/menu/ingresos", {
        templateUrl: "/templates/ingresos/menu.html"
    })
    .when("/ingresos/efectivo", {
        templateUrl: "/templates/ingresos/efectivo.html"
    })
    .when("/ingresos/bloque", {
        templateUrl: "/templates/ingresos/bloque.html"
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
    .when("/pazysalvo/document/:id", {
        templateUrl: "/templates/pazysalvo/document.html"
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

    $scope.realizarCobroMulta = function ()
    {
        closeModal('cobrosMulta');
        $http.post('/propietarios/cobro/multa')
        .success(function(data, status, headers, config)
        {
            alert('Se cargaron los cobros de multa correctamente')
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
    };

    $scope.verificarPazYSalvo= function()
    {
        $http.get('/propietarios/pazysalvo/'+$scope.propiedad_pazysalvo)
            .success(function(data, status, headers, config)
            {
                closeModal('pazysalvo');
                window.location.href= '#/pazysalvo/document/'+$scope.propiedad_pazysalvo;
            })
            .error(function(error, status, headers, config)
            {
                closeModal('pazysalvo');
                alert("La propiedad no se encuentra al dia con los pagos");
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


app.controller("EgresosTotalesController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.acumulado = 0;
        $scope.date1 = '', $scope.date2= '';
        $scope.egresos = [], $scope.total=0;

        // init http request
        $scope.search = function(page)
        {
            $scope.egreso = [], $scope.total = 0;
            $http.get('/egresos/between/'+$scope.date1+'/'+$scope.date2)
                .success(function(data, status, headers, config)
                {
                    $scope.acumulado = 0;
                    for(i=0; i<data.length; i++){
                        $scope.acumulado += data[i].valor
                    }
                    $scope.egresos = data;
                    $scope.total=$scope.egresos.length;
                    $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { concepto : 'asc'}}, {
                        total: $scope.egresos.length,
                        getData: function($defer, params)
                        {
                            TableService.getTable($defer,params,$scope.filter, $scope.egresos);
                        }
                    });
                    $scope.tableParams.reload();
                    $scope.$watch("filter.$", function () {
                        $scope.tableParams.reload();
                    });
                });
        };


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

app.controller("ReporteTotalDetail",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams', function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params)
{
    $scope.periodo = $params.periodo;

    $scope.values = $scope.periodo.split('-');
    $scope.id = $params.id;
    $scope.ingresos = 0;

    $scope.pagos = [], $scope.total=0;
    $scope.listar = function(page)
    {
        $http.get('/usuarios/ingresos/detail-totales/'+$scope.values[1]+'/'+$scope.values[0]+'/'+$scope.id)
            .success(function(data, status, headers, config)
            {
                $scope.pagos = data;
                $scope.ingresos = 0;

                for(i=0 ; i< $scope.pagos.length; i++){
                    $scope.ingresos += $scope.pagos[i].ingresos;
                }
                $scope.total=$scope.pagos.length;
                $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { codigo_cobro: 'asc'}}, {
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

app.controller("IngresosEfectivoDetailController",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams', function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params)
{
    $scope.periodo = $params.periodo;

    $scope.values = $scope.periodo.split('-');
    $scope.id = $params.id;
    $scope.ingresos = 0;

    $scope.pagos = [], $scope.total=0;
    $scope.listar = function(page)
    {
        $http.get('/usuarios/ingresos/detail-efectivos/totales/'+$scope.values[1]+'/'+$scope.values[0]+'/'+$scope.id+'/EFECTIVO')
            .success(function(data, status, headers, config)
            {
                $scope.pagos = data;
                $scope.ingresos = 0;

                for(i=0 ; i< $scope.pagos.length; i++){
                    $scope.ingresos += $scope.pagos[i].ingresos;
                }
                $scope.total=$scope.pagos.length;
                $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { codigo_cobro: 'asc'}}, {
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

app.controller("PazySalvoDocument",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams', function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params)
{
    $scope.propiedad = $params.id;
    $scope.date = moment().format('MMMM Do YYYY, h:mm:ss a');

    console.log('entro')
    $scope.imprimir = function()
    {
        if($scope.propiedad)
        {

            window.print();
        }
    }
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

        $scope.validate = function (value)
        {
            if(value>0)
                return true;
            return false;
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

        $scope.validate = function (value) {
            if(value>0)
                return true;
            return false;
        }

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

app.controller("IngresosBloqueController", [
    '$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout', function($scope, $http, $filter, ngTableParams, TableService, $timeout)
    {
        $scope.ingresos = [],$scope.matrizIngresos= [];


        $scope.excel= function()
        {
            var blob = new Blob([document.getElementById('bajar').innerHTML], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
            });
            saveAs(blob, "Descarga Ingresos Bloques.xls");
        };

        $scope.obtenerReporte = function()
        {
            $http.get('/usuarios/ingresos/bloques')
                .success(function(data, status, headers, config)
                {
                    $scope.ingresos = data;
                    $scope.matrizIngresos =[];

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        if($scope.ValidateMatriz(ingreso.propiedad_id) == -1)
                        {
                            $scope.matrizIngresos.push({
                                bloque: ingreso.bloque,
                                propiedad_id : ingreso.propiedad_id,
                                enero : {total :0, id :0},
                                febrero: {total :0, id :0},
                                marzo: {total :0, id :0},
                                abril:{total :0, id :0},
                                mayo : {total :0, id :0},
                                junio: {total :0, id :0},
                                julio: {total :0, id :0},
                                agosto:{total :0, id :0},
                                septiembre:{total :0, id :0},
                                octubre:{total :0, id :0},
                                noviembre :{total :0, id :0},
                                diciembre:{total :0, id :0}})
                        }
                    });

                    $scope.ingresos.forEach(function(ingreso)
                    {
                        index = $scope.ValidateMatriz(ingreso.propiedad_id);
                        if(ingreso.month == 1)
                        {
                            $scope.matrizIngresos[index].enero.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].enero.id = ingreso.id;
                        }
                        else if(ingreso.month == 2)
                        {
                            $scope.matrizIngresos[index].febrero.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].febrero.id = ingreso.id;
                        }
                        else if(ingreso.month == 3)
                        {
                            $scope.matrizIngresos[index].marzo.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].marzo.id = ingreso.id;
                        }
                        else if(ingreso.month == 4)
                        {
                            $scope.matrizIngresos[index].abril.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].abril.id = ingreso.id;
                        }
                        else if(ingreso.month == 5)
                        {
                            $scope.matrizIngresos[index].mayo.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].mayo.id = ingreso.id;
                        }
                        else if(ingreso.month == 6)
                        {
                            $scope.matrizIngresos[index].junio.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].junio.id = ingreso.id;
                        }
                        else if(ingreso.month == 7)
                        {
                            $scope.matrizIngresos[index].julio.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].julio.id = ingreso.id;
                        }
                        else if(ingreso.month == 8)
                        {
                            $scope.matrizIngresos[index].agosto.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].agosto.id = ingreso.id;
                        }
                        else if(ingreso.month == 9)
                        {
                            $scope.matrizIngresos[index].septiembre.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].septiembre.id = ingreso.id;
                        }
                        else if(ingreso.month == 10)
                        {
                            $scope.matrizIngresos[index].octubre.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].octubre.id = ingreso.id;
                        }
                        else if(ingreso.month == 11)
                        {
                            $scope.matrizIngresos[index].noviembre.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].noviembre.id = ingreso.id;
                        }
                        else if(ingreso.month == 12)
                        {
                            $scope.matrizIngresos[index].diciembre.total = ingreso.valor_pagado;
                            $scope.matrizIngresos[index].diciembre.id = ingreso.id;
                        }
                    });
                    console.log('matriz', $scope.matrizIngresos);
                });
        };

        $scope.verAbonos = function(id)
        {
            $http.get('/propietarios/abonos/pago/'+id)
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

        $scope.imprimir = function()
        {
            window.print();
        }

        $scope.ValidateMatriz = function(propiedad_id)
        {
            var posicion= -1;
            $scope.matrizIngresos.forEach(function(ingreso, index)
            {
                if(ingreso.propiedad_id == propiedad_id)
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

app.controller("EgresosTotalesDetailController",['$scope', '$http', '$filter', 'ngTableParams', 'TableService', '$timeout','$routeParams',
    function($scope, $http, $filter, ngTableParams, TableService, $timeout, $params) {
    $scope.egresos = [];
    $scope.total=0;
    $scope.date1 = $params.date1;
    $scope.date2 = $params.date2;
    $scope.concept = $params.concept;
    $scope.acumulado = 0;
    $scope.search = function(page)
    {
        $http.get('/egresos/concept/'+$params.date1+'/'+$params.date2+'/'+$params.concept)
            .success(function(data, status, headers, config)
            {
                $scope.acumulado = 0;
                for(i=0; i<data.length; i++){
                    $scope.acumulado += data[i].valor
                }
                $scope.egresos = data;
                $scope.total=$scope.egresos.length;
                $scope.tableParams = new ngTableParams({page:1, count:10, sorting: { id: 'asc'}}, {
                    total: $scope.egresos.length,
                    getData: function($defer, params)
                    {
                        TableService.getTable($defer,params,$scope.filter, $scope.egresos);
                    }
                });
                $scope.tableParams.reload();
                $scope.$watch("filter.$", function () {
                    $scope.tableParams.reload();
                });
            });
    };
    $scope.search();
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
