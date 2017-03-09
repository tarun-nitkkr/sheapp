<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>

    <head>

        <title>Happy Women's Day</title>
        <style type="text/css">
            /*body {
              min-height: 1000px;
              min-width: 1024px;
            }*/
            /* Google Fonts */
            @import url(https://fonts.googleapis.com/css?family=Anonymous+Pro);

            /* Global */
            html{
                min-height: 100%;
                overflow: auto;
            }
            body{
                height: calc(100vh - 8em);
                padding: 4em;
                color: rgba(255,255,255,5);
                font-family: 'Anonymous Pro', monospace;  
                background-color: rgb(25,25,25);  
            }
            .line-1{
                position: relative;
                top: 50%;  
                width: 50em;
                margin: 0 auto;
                border-right: 2px solid rgba(255,255,255,.75);
                font-size: 120%;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                transform: translateY(-50%);    
            }

            .line-2{
                position: relative;
                top: 50%;  
                width: 120em;
                margin: 0 auto;
                border-right: 2px solid rgba(255,255,255,.75);
                font-size: 120%;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                transform: translateY(-50%);    
            }

            /* Animation */
            .anim-typewriter{
                animation: typewriter 4s steps(35) 1s 1 normal both,
                    blinkTextCursor 500ms steps(35) 10 normal;
            }

            .anim-typewriter2{
                animation: typewriter 4s steps(35) 6s 1 normal both,
                    blinkTextCursor 500ms steps(35) 6s 10 normal;
            }

            .anim-typewriter3{
                animation: typewriter3 4s steps(35) 12s 1 normal both,
                    blinkTextCursor 500ms steps(35) 12s 10 normal;
            }

            .anim-typewriter4{
                animation: typewriter4 4s steps(35) 17s 1 normal both,
                    blinkTextCursor 500ms steps(35) 17s 10 normal;
            }

            .anim-typewriter5{
                animation: typewriter5 4s steps(35) 22s 1 normal both,
                    blinkTextCursor 500ms steps(35) 22s 10 normal;
            }

            .anim-typewriter6{
                animation: typewriter3 4s steps(35) 27s 1 normal both,
                    blinkTextCursor 500ms steps(35) 27s 10 normal;
            }

            .anim-typewriter7{
                animation: typewriter 4s steps(35) 34s 1 normal both,
                    blinkTextCursor 500ms steps(35) 34s 10 normal;
            }

            .anim-typewriter8{
                animation: typewriter5 4s steps(35) 40s 1 normal both,
                    blinkTextCursor 500ms steps(35) 40s 10 normal;
            }

            @keyframes typewriter{
                from{width: 0;}
                to{width: 30em;}
            }
            @keyframes typewriter3{
                from{width: 0;}
                to{width: 42em;}
            }

            @keyframes typewriter4{
                from{width: 0;}
                to{width: 50em;}
            }

            @keyframes typewriter5{
                from{width: 0;}
                to{width: 24em;}
            }
            @keyframes blinkTextCursor{
                from{border-right-color: rgba(255,255,255,.75);}
                to{border-right-color: transparent;}
            }

            img {
                opacity:0;
                animation: 2s pulse 45s forwards;
            }

            @keyframes pulse {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }

        </style>
    </head>
    <body>
        <p class="line-1 anim-typewriter">They say behind every successful man, there is a WOMAN,</p>
        <p class="line-2 anim-typewriter2">But behind this man, there is a successful WOMAN.</p>
        <p class="line-2 anim-typewriter3">I would like to use this opportunity to tell you something which is true, </p>
        <p class="line-2 anim-typewriter4" style="font-size:140%; color:rgb(33, 255, 33);">"You are an amazing person having wonderful capabilities which even you are not aware of,</p>
        <p class="line-2 anim-typewriter5" style="font-size:140%; color:rgb(33, 255, 33);">you can achieve anything you want,</p>
        <p class="line-2 anim-typewriter6" style="font-size:140%; color:rgb(33, 255, 33);">you just need a sincere motivation and a fearless momentum.</p>
        <p class="line-2 anim-typewriter7" style="font-size:140%; color:rgb(33, 255, 33);">So follow your dreams and make them come true".</p>
        <p class="line-2 anim-typewriter8" style="font-size:160%; color: pink;">HAPPY WOMEN'S DAY, SWEETHEART</p>


        <img src="/test/img_bike.jpg">

    </body>
</html>