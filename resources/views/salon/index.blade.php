<!DOCTYPE  html>
<head>
    <meta charset="iso-8859-1" lang="en">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <title>MultiFundadores Principal</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/templatemo_main.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="/css/resetcasa.css">
    <link rel="stylesheet" type="text/css" href="/css/responsivecasa.css">

    <script type="text/javascript" src="/js/jquerycasa.js"></script>
    <script type="text/javascript" src="/js/maincasa.js"></script>
</head>

<body>
<section class="hero">
    <header>
        <div class="wrapper">

            <a href="#" class="hamburger"></a>
            <nav>
                <ul>
                    <li><a href="#">Eventos</a></li>
                    <li><a class="active" href="/salon/comunal">Sal&oacute;n comunal</a></li>
                    <li><a href="#">Reglamento</a></li>
                </ul>
                <a href="/usuarios/login" class="login_btn">Usuarios</a>
                <a href="/propietarios/login" class="login_btn">Propietarios</a>
            </nav>
        </div>
    </header><!--  end header section  -->

    <section>
        <div class="container">
            <div class="row">
                <div class="table-responsive">
                    <table style="color:white" class="table table-bordered">
                        <thead>
                        <tr>
                            <td>Fecha Inicial</td>
                            <td>Fecha Final</td>
                            <td>Descripcion</td>
                        </tr>
                        </thead>
                        <tbody id="table" >
                        @foreach($reservas as $reserva)
                            <tr>
                                <td>{{$reserva->fecha_inicial}}</td>
                                <td>{{$reserva->fecha_final}}</td>
                                <td>{{$reserva->descripcion}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</section><!--  end hero section  -->

<footer>
    <div class="copyrights wrapper">
        Copyright © 2015 <a href="http://multifundadores.com">Multifundadores.com</a>
    </div>
</footer><!--  end footer  -->
</body>
</html>

