<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    <title> Email Verification</title>
</head>

<body style="font-family: 'Rubik', sans-serif; background-color: #FAFAFA; width:100%; padding:0px; margin: 0px">

  <div style="Width:100%">
     <div style="Width:100%; height:130px; background: #6610f2;">
       <img style="width:16%; height:100px; margin-right:42%; margin-left:40%; margin-top:20px;">
     </div>

     <div style="Width:70%; margin-left:10%; min-height:300px; background: #FAFAFA;; border:1px solid #70bbd9; margin-top:30px; padding:5%">
         <h1> Welcome to <span style="color: #6610f2;">Bartum Energy</span></h1><br>

         Please confirm your email by clicking the link below <br>

<a href="{{route('confirmemail', $user->link)}}"> {{route('confirmemail', $user->link)}} </a>
<br>





</div><br><br><br><br>

<div style="Width:100%; height:150px; background: #6610f2;">

</div>

  </div>

</body>

</html>
