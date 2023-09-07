<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="col-md-8 offset-md-2">
                <p>
                                    <li><a href="#" id="main_modal"  data-title="Add Case Note ">Add Case Note</a></li>

                </p>
            </div>
        </main>
    </div>



     <!-- Main Modal -->
    <div id="main_modal" class="modal animated bounceInDown" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="modal-btn btn btn-danger btn-sm pull-right" data-dismiss="modal"><i class="glyphicon glyphicon-remove-circle"></i> Exit</button>
            <button type="button" id="modal-fullscreen" class="modal-btn btn btn-primary btn-sm pull-right"><i class="glyphicon glyphicon-fullscreen"></i> Full Screen</button>
            <h5 class="modal-title"></h5>
          </div>
          <div class="alert alert-danger" style="display:none; margin: 15px;"></div>
          <div class="alert alert-success" style="display:none; margin: 15px;"></div>
          <div class="modal-body" style="overflow:hidden;"></div>
        </div>

      </div>
    </div>




<script src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <script type="text/javascript">
        //Ajax Modal Function
    $(document).on("click",".ajax-modal",function(){
         var link = $(this).attr("href");
         var title = $(this).data("title");
         var fullscreen = $(this).data("fullscreen");
         $.ajax({
             url: link,
             beforeSend: function(){
                $("#preloader").css("display","block"); 
             },success: function(data){
                $("#preloader").css("display","none");
                $('#main_modal .modal-title').html(title);
                $('#main_modal .modal-body').html(data);
                $("#main_modal .alert-success").css("display","none");
                $("#main_modal .alert-danger").css("display","none");
                $('#main_modal').modal('show'); 
                
                if(fullscreen ==true){
                    $("#main_modal >.modal-dialog").addClass("fullscreen-modal");
                }else{
                    $("#main_modal >.modal-dialog").removeClass("fullscreen-modal");
                }
                
                //init Essention jQuery Library
                $("select.select2").select2();
                $('.year').mask('0000-0000');
                $(".ajax-submit").validate();
                $(".datepicker").datepicker();  
                $(".dropify").dropify();
                $("input:required, select:required, textarea:required").prev().append("<span class='required'> *</span>");
             }
         });
         
         return false;
     }); 
     
     $("#main_modal").on('show.bs.modal', function () {
         $('#main_modal').css("overflow-y","hidden");       
     });
     
     $("#main_modal").on('shown.bs.modal', function () {
        setTimeout(function(){
          $('#main_modal').css("overflow-y","auto");
        }, 1000);   
     });

     
     //Ajax Modal Submit
     $(document).on("submit",".ajax-submit",function(){
        var elem = $(this);
        $(elem).find("button[type=submit]").prop("disabled",true);       
        var link = $(this).attr("action");
        $.ajax({
             method: "POST",
             url: link,
             data:  new FormData(this),
             mimeType:"multipart/form-data",
             contentType: false,
             cache: false,
             processData:false,
             beforeSend: function(){
               button_val = $(elem).find("button[type=submit]").text();
               $(elem).find("button[type=submit]").html('<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>');
             
             },success: function(data){
                window.setTimeout(function(){
                    $(elem).find("button[type=submit]").html(button_val);
                    $(elem).find("button[type=submit]").attr("disabled",false);
                }, 1000);
                
                var json = JSON.parse(data);
                if(json['result'] == "success"){
                    $("#main_modal .alert-success").html(json['message']);
                    $("#main_modal .alert-success").css("display","block");
                    // if(json['action'] == "update"){
                    //  $('#row_'+json['data']['id']).find('td').each (function() {
                    //     if(typeof $(this).attr("class") != "undefined"){
                    //         $(this).html(json['data'][$(this).attr("class")]);
                    //     }
                    //  });  
                        
                    // }else if(json['action'] == "store"){
                    //  $('.ajax-submit')[0].reset();
                    //  //store = true;
                        
                    //  var new_row = $("table").find('tr:eq(1)').clone();
                        
                    //  $(new_row).attr("id", "row_"+json['data']['id']);
                        
                    //  $(new_row).find('td').each (function() {
                    //     if($(this).attr("class") == "dataTables_empty"){
                    //         window.location.reload();
                    //     }    
                    //     if(typeof $(this).attr("class") != "undefined"){
                    //         $(this).html(json['data'][$(this).attr("class")]);
                    //     }
                    //  }); 
                        
                    //  var url  = window.location.href; 
                    //  $(new_row).find('form').attr("action",url+"/"+json['data']['id']);
                    //  $(new_row).find('.btn-warning').attr("href",url+"/"+json['data']['id']+"/edit");
                    //  $(new_row).find('.btn-info').attr("href",url+"/"+json['data']['id']);
                        
                    //  $("table").prepend(new_row);
        
                    //  //window.setTimeout(function(){window.location.reload()}, 2000);
                    // }
                    location.reload()
                }else{
                    if(json['message_type'] == "toast"){
                        jQuery.each( json['message'], function( i, val ) {
                           Command: toastr["error"](val);
                        });
                    }else{
                        jQuery.each( json['message'], function( i, val ) {
                           $("#main_modal .alert-danger").append("<p>"+val+"</p>");
                        });
                        $("#main_modal .alert-danger").css("display","block");
                    }
                }
             }
         });

         return false;
     });
     
     //Ajax submit with validate
     $(".appsvan-submit-validate").validate({
         submitHandler: function(form) {
             var elem = $(form);
             $(elem).find("button[type=submit]").prop("disabled",true);
             var link = $(form).attr("action");
             $.ajax({
                 method: "POST",
                 url: link,
                 data:  new FormData(form),
                 mimeType:"multipart/form-data",
                 contentType: false,
                 cache: false,
                 processData:false,
                 beforeSend: function(){
                   button_val = $(elem).find("button[type=submit]").text();
                   $(elem).find("button[type=submit]").html('<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>');
                 
                 },success: function(data){             
                    var json = JSON.parse(data);
                    if(json['result'] == "success"){
                        Command: toastr["success"](json['message']);
                    }else{
                    $(elem).find("button[type=submit]").html(button_val);
                    $(elem).find("button[type=submit]").attr("disabled",false);
                        jQuery.each( json['message'], function( i, val ) {
                           Command: toastr["error"](val);
                        });
                    }
                 }
             });

            return false; 
        },invalidHandler: function(form, validator) {},
          errorPlacement: function(error, element) {}
     });
     
     //Ajax submit without validate
     $(document).on("submit",".appsvan-submit",function(){       
         var elem = $(this);
         $(elem).find("button[type=submit]").prop("disabled",true);
         var link = $(this).attr("action");
         $.ajax({
             method: "POST",
             url: link,
             data:  new FormData(this),
             mimeType:"multipart/form-data",
             contentType: false,
             cache: false,
             processData:false,
             beforeSend: function(){
               button_val = $(elem).find("button[type=submit]").text();
               $(elem).find("button[type=submit]").html('<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>');
             
             },success: function(data){             
                var json = JSON.parse(data);
                if(json['result'] == "success"){
                    Command: toastr["success"](json['message']);
                    location.reload()
                }else{
                    $(elem).find("button[type=submit]").html(button_val);
                    $(elem).find("button[type=submit]").attr("disabled",false);
                    jQuery.each( json['message'], function( i, val ) {
                       Command: toastr["error"](val);
                    });
                    
                }
             }
         });

         return false;
     });
     
     
    $("#main_modal").on("hidden.bs.modal", function () {

    });
    </script>
</body>
</html>
