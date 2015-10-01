<!DOCTYPE html>
<head>
    <meta charset="iso-8859-1" lang="en">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <title>MultiFundadores Principal</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/templatemo_main.css">

</head>
<body>
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
<div class="template-page-wrapper">
    <div class="navbar-collapse collapse templatemo-sidebar">
        <ul class="templatemo-sidebar-menu">
            <li>
                <form class="navbar-form">
                    <label><h1 style="font-size: medium">Bienvenido, {{ Auth::user()->nombre }} </h1></label>
                </form>
            </li>
            <li class="active"><a href="/usuarios/home"><i class="fa fa-home"></i>Men&uacute; principal</a></li>

            <li>
                <a href="#"><i class="fa fa-money"></i> Ingresos <div class="pull-right"></div></a>
            </li>
            <li><a href="#"><i class="fa fa-credit-card"></i><span class="badge pull-right">9</span>Egresos</a></li>
            <li><a href="/propietarios/create"><i class="fa fa-users"></i><span class="badge pull-right">42</span>Propietarios</a></li>
            <li><a href="#"><i class="fa fa-building"></i><span class="badge pull-right">NEW</span>Apartamentos</a></li>
            <li><a href="#" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-sign-out"></i>Salir</a></li>
        </ul>
    </div><!--/.navbar-collapse -->

    <div class="container">
        @yield('content')
    </div>

</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/Chart.min.js"></script>
<script src="/js/templatemo_script.js"></script>
<script type="text/javascript">
    // Line chart
    var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
    var lineChartData = {
        labels : ["January","February","March","April","May","June","July"],
        datasets : [
            {
                label: "My First dataset",
                fillColor : "rgba(220,220,220,0.2)",
                strokeColor : "rgba(220,220,220,1)",
                pointColor : "rgba(220,220,220,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(220,220,220,1)",
                data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
            },
            {
                label: "My Second dataset",
                fillColor : "rgba(151,187,205,0.2)",
                strokeColor : "rgba(151,187,205,1)",
                pointColor : "rgba(151,187,205,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(151,187,205,1)",
                data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
            }
        ]

    }

    window.onload = function(){
        var ctx_line = document.getElementById("templatemo-line-chart").getContext("2d");
        window.myLine = new Chart(ctx_line).Line(lineChartData, {
            responsive: true
        });
    };

    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $('#loading-example-btn').click(function () {
        var btn = $(this);
        btn.button('loading');
        // $.ajax(...).always(function () {
        //   btn.button('reset');
        // });
    });
</script>
</body>
</html>