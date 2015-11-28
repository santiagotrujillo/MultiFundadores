<!DOCTYPE html>
<head>
    <meta charset="iso-8859-1" lang="en">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <title>MultiFundadores Principal</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/templatemo_main.css">
    <link rel="stylesheet" href="/css/ng-table.min.css">

</head>
<body ng-app="multifundadores">

<div class="navbar navbar-inverse" role="navigation">
    <div class="navbar-header">
        <div class="logo"><h1>Pagina principal - Administrador</h1></div>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
</div>
<div class="template-page-wrapper" >
    <base href="/usuarios/home/">

    <div class="navbar-collapse collapse templatemo-sidebar">
        <ul class="templatemo-sidebar-menu">
            <li>
                <form class="navbar-form">
                    <label><h1 style="font-size: medium">Bienvenido, {{ Auth::user()->get()->nombre }} </h1></label>
                </form>
            </li>
            <li class="active"><a href="#operaciones"><i class="fa fa-home"></i>Men&uacute; principal</a></li>

            <li><a href="#recaudos"><i class="fa fa-money"></i> Recaudos</a></li>
            <li><a href="#egresos"><i class="fa fa-credit-card"></i> Egresos</a></li>
            <li><a href="#propietario"><i class="fa fa-users"></i>Propietarios</a></li>
            <li><a href="#pagos"><i class="fa fa-bank"></i>Pagos Totales</a></li>
            <li><a href="/usuarios/salir"><i class="fa fa-sign-out"></i>Salir</a></li>
        </ul>
    </div><!--/.navbar-collapse -->

    <div class="container">
        <div class="row">
            <div class="col-md-offset-2"ng-view>

            </div>
        </div>
    </div>


</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/Chart.min.js"></script>
<script src="/js/templatemo_script.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
<script src="/js/ng-table.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>