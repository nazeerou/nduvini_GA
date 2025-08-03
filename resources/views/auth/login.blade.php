@extends('layouts.app')
@section('content')

<div class="wrapper">
            <div class="container">
                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <span class="form-title1">Garage Assistant (GA) </span>
                    <span class="form-title"> Login  </span>
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <span class="fa fa-envelope" aria-hidden="true"></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Username" required />
                            @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
						 @enderror
                        </div>
                    </div>    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <span class="fa fa-lock" aria-hidden="true"></span>
                            <input id="password-field" type="password" name="password" class="form-control  @error('password') is-invalid @enderror" value="" placeholder="Password" required>                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                           @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="btn btn-primary btn-block btn-lg" value="Login">
                    </div>
                </form>
                <div class="login-left">
                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                            <!-- <li data-target="#carouselExampleIndicators" data-slide-to="1"></li> -->
                            <!-- <li data-target="#carouselExampleIndicators" data-slide-to="2"></li> -->
                        </ol>
                        <div class="carousel-inner">
                        <div class="carousel-item active">
                        <img class="d-block w-100" height="80%" src="{{ asset('/assets/images/garage.png') }}" alt="First slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block w-100" height="80%" src="{{ asset('/assets/images/garage.png') }}" alt="Second slide">
                            </div>

                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

<script>
    $(".toggle-password").click(function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
    input.attr("type", "text");
    } else {
    input.attr("type", "password");
    }
});
</script>
@endsection