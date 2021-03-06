@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body" style="text-align: center">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Dziękujemy, twój adres e-mail został potwierdzony!
                    <br>Możesz teraz zalogowac się w aplikacji.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
