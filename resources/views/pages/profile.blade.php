@extends('layouts.app_header')

@section('content')
<style>

.profile-pic {
     max-width: 100px;
    max-height: 100px;
    display: block; 
}
.file-upload {
    display: none;
}
.circle {
    border-radius: 1000px !important;
    /* overflow: hidden; */
    /* width: 128px; */
    /* height: 128px; */
    border: 8px solid rgba(255, 255, 255, 0.7);
    /* position: absolute; */
    top: 2px;
}
img {
    max-width: 100%;
    height: auto;
}
.p-image {
  position: absolute;
  top: 77px; 
  right: 0;
  color: #666666;
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
}
.p-image:hover {
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
}
.upload-button {
  font-size: 1.7em;
}

.upload-button:hover {
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
  color: #999;
}  
</style>
<!-- <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script> -->
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Profile </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h3 class="panel-title"> My Profile </h3>
            </div>
        
            <div class="panel-body">
            <div class="row">
             <div class="col-lg-2"> 
            <form id="formPost" method="POST" action="{{ url('/profile/update') }}" enctype="multipart/form-data"> 
                  @csrf
            <div class="user-box" style="padding: 0px 0px 0px 0px; width: 100%;">
            <div class="user-img">    
            <div class="circle">
            <!-- User Profile Image -->
            @if(!Auth::user()->profile_img)
            <img src="assets/images/users/profile.jpg" alt="User profile" title="User" class="img-circle img-thumbnail img-responsive profile-pic">
            @else
            <img src="{{ url('/attachments/'.Auth::user()->profile_img) }}" alt="User profile" title="User" class="img-circle img-thumbnail img-responsive profile-pic">
            @endif
            <!-- Default Image -->
            </div>
            <div class="p-image">
               <i class="fa fa-edit upload-button"></i>
                <input class="file-upload" type="file" name="profile_img"  style="display: none;"/>
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}"/>
              </div>     
            </div>  <br/><br/>
            <button type="submit" class="btn btn-sm" style="border: 1px solid #abb;"> Update Photo </button>    
          </form>
                <h5><a href="#"> {{ Auth::user()->fname }} {{ Auth::user()->lname }}</a> </h5>
                        <ul class="list-inline">
                            <li>
                                <a href="user-profile" >
                                    <!-- <i class="zmdi zmdi-settings"></i> -->
                                </a>
                            </li>
                        </ul>
                    </div>
                             </div>
                            <div class="col-lg-10">
                                <ul class="nav nav-tabs">
                                    <li role="presentation" class="active">
                                        <a href="#home1" role="tab" data-toggle="tab">My Profile</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#profile1" role="tab" data-toggle="tab">Change Password</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="home1">
                                        <p>

                                        <table id="datatable" class="table table-striped table-bordered">
                                            <tbody>
                                            @foreach($users as $user)
                                                <tr>
                                                   <td> Full Name </td> <td> {{ $user->fname }} {{ $user->lname }} </td>
                                                   </tr>
                                                   <tr>
                                                   <td> Email </td><td> {{ $user->email }} </td>
                                                   </tr>
                                                   <tr>
                                                   <td> Mobile </td><td> {{ $user->mobile }} </td>
                                                   </tr>
                                                   <tr>
                                                    <td> Status </td><td> @if($user->is_admin == 1) {{ 'Admin '}}  @else {{ 'Staff' }} @endif </td>
                                                </tr>
                                            @endforeach  
                                                </tbody>
                                            </table>
                                        </p>
                                    </div>


                                    <div role="tabpanel" class="tab-pane fade" id="profile1">
                                    <p> <br/>
										<form class="" action="{{ url('/change_password')}}" method="POST" enctype="multipart/form-data"> 
                                             @csrf
												
											<div class="form-group">
												<div class="row align-items-center">
													<label class="col-sm-2"> Current Password </label>
													<div class="col-sm-7">
														<input type="password" name="current_password" class="form-control" placeholder="Current Password">
													</div>
												</div>
											</div>
                                            
                                            <div class="form-group">
												<div class="row">
													<label class="col-sm-2">New Password </label>
													<div class="col-sm-7">
                                                          <input type="password" class="form-control" name="new_password" placeholder="New Password">
													</div>
												</div>
											</div>

                                            <div class="form-group">
												<div class="row">
													<label class="col-sm-2">Confirm Password </label>
													<div class="col-sm-7">
                                                          <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">
													</div>
												</div>
											</div>
											
                                         <div class="row">
                                             <div class="col-md-2">
                                            </div>
                                           <div class="col-md-10">
                                           <div class="btn-list mt-4 text-left">
												<button type="submit" class="btn btn-info btn-space" id="btn_save">Update</button>
											  </div>
                                            </div>
                                         </div>
										</form>
                                    </p>                                   
                                </div>
                            </div><!-- end col -->         
                        </div>
                        <!-- end row -->
              </div>
          </div>
      </div>
    </div>
</div>
<!-- END  -->

<script>
$(document).ready(function() {

   
var readURL = function(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.profile-pic').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$(".file-upload").on('change', function(){
    readURL(this);
});

$(".upload-button").on('click', function() {
   $(".file-upload").click();
});
});
</script>
@endsection