
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    <title>PASSWORD RESET LINK</title>
</head>

<body style="font-family: 'Rubik', sans-serif;  rgba(151, 151, 151, 0.04);">

  <div style="background: rgba(151, 151, 151, 0.04); width:100%; min-height:600px; font-family: Montserrat;
  font-size: 30px;
  font-style: normal;
  font-weight: 400;
  line-height: 37px;
  letter-spacing: 0em;
  text-align: center;">

    <img src="{{asset('assets/man.png')}}" alt="">

    Dear {{$data['firstname'] }},<br><br>

  You've asked to reset your  password. Click the button below to reset your password:<br><br>


                                      <a href="{{{$data['link']}}}">  <button style="height: 70px;width: 300px;border-radius: 6px;"> Reset Password</button> </a>
                                        <br><br>


              You can also copy and paste this link into your browser:<br><br>

  {{{$data['link']}}}

  <br><br>
  Note: You can only use this e-mail to reset your password within 24 hours.<br><br>




  </div>



</body>

</html>
