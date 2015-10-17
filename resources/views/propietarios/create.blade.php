@extends('users.home')
@section('content')

    <a href="/templates/propietarios/list.html">testing</a>
    <div class="row">
        <br/>
        <div class="col-md-offset-2">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Error.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(Session::has('propietario.create'))
                <div class="alert alert-success">
                    {{Session::get('propietario.create')}}
                </div>
            @endif
            <form action="/propietarios/create" method="post">
                <div class="col-md-6">
                    <label>Id*</label>
                    <input type="text" name="id" class="form-control"  placeholder="Digite el Nombre">
                </div>
                <div class="col-md-6">
                    <label>Nombre*</label>
                    <input type="text" name="nombre" class="form-control"  placeholder="Digite el Nombre">
                </div>
                <div class="col-md-6">
                    <label>Apellido*</label>
                    <input type="text"  name="apellido" class="form-control"  placeholder="Digite el apellido">
                </div>
                <div class="col-md-6">
                    <label>Telefono</label>
                    <input type="numeric" name="telefono" class="form-control"  placeholder="Digite el telefono">
                </div>
                <div class="col-md-6">
                    <label>Clave*</label>
                    <input type="password" name="clave" class="form-control"  placeholder="Digite la clave">
                </div>

                <div class="col-md-12">
                    <br/>
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
    </div>

@endsection

