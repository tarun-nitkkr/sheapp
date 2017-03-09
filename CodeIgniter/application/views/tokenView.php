<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Simple Try</title>

	<!--Pulling Awesome Font -->
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-offset-5 col-md-3">
            <div class="form-login">
            <h4>Welcome back.</h4>
            <input type="text" id="userName" class="form-control input-sm chat-input" placeholder="username" />
            </br>
            <input type="text" id="userPassword" class="form-control input-sm chat-input" placeholder="password" />
            </br>
            <div class="wrapper">
            <span class="group-btn">     
                <a onclick="login();" class="btn btn-primary btn-md">login <i class="fa fa-sign-in"></i></a>
            </span>
            </div>
            </div>
        
        </div>
    </div>
</div>

</body>
<script>
    function login() {
        var uname = $("#userName").val();
        var pass = $("#userPassword").val();
        $.ajax({
            type: "GET",
            url: "/do/dataquality/OAController/seachBwiwMobile",
            data: {
                MOBILE: mobile
            },
            dataType: 'JSON',
            success: function (data, textStatus, jqXHR) {
                if (data.STATUS) {
                    $("#searchResult").html(data.MSG);
                }

            }

        });
    }
    </script>
</html>