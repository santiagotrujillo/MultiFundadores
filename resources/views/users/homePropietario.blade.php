<script>var session = {{ Auth::owner()->get()->id }}</script>
<!DOCTYPE html>
<head>
    <meta charset="iso-8859-1" lang="en">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <title>MultiFundadores Principal - Propietario</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/templatemo_main.css">
    <link rel="stylesheet" href="/css/ng-table.min.css">

</head>
<body ng-app="multifundadores">

<div class="navbar navbar-inverse" role="navigation">
    <div class="navbar-header">
        <div class="logo"><h1>Pagina principal - Propietario</h1></div>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
</div>
<div class="template-page-wrapper" >
    <base href="/propietarios/home/">

    <div class="navbar-collapse collapse templatemo-sidebar">
        <ul class="templatemo-sidebar-menu">
            <li>
                <form class="navbar-form">
                    <label><h1 style="font-size: medium">Bienvenido, {{Auth::owner()->get()->nombre}} </h1></label>
                </form>
            </li>
            <li class="active"><a href="#propiedades"><i class="fa fa-home"></i>Men&uacute; principal</a></li>
            <li><a href="#propiedades"><i class="fa fa-building"></i>Propiedades</a></li>
            <li><a href="/propietarios/salir"><i class="fa fa-sign-out"></i>Salir</a></li>
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
<script src="/js/propietario.js"></script>
</body>
</html>
