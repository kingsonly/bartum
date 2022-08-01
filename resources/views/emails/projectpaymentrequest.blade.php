
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    <title>Project Payment Request</title>
</head>

<body style="font-family: 'Rubik', sans-serif;  rgba(151, 151, 151, 0.04);">

  <div style="background: rgba(151, 151, 151, 0.04); width:100%; min-height:600px; font-family: Montserrat;
  font-size: 20px;
  font-style: normal;
  font-weight: 400;
  line-height: 37px;
  letter-spacing: 0em;
  text-align: center;
  padding-top:20px;
  ">

    <img src="https://api.bartumenergy.com/logo.jpeg" alt="">
    <br><br>

    Dear {{$data['firstname'] }},<br><br>
    Thank you for your interest in Bartum Energy, <br/> Your request is being processed and you are required to make a payment of ₦{{{$data['amount']}}}
    <br>
    Please Click on the button below to complete your transaction.<br><br>

    
                                      <a href="https://admin.bartumenergy.com/payment-plan/{{{$data['link']}}}" style="text-decoration: none;"> 
                                         <button style="height: 70px;width: 300px;border-radius: 6px;background:rgb(135, 199, 110);color:#fff;font-size:18px"> Make Payment</button> 
                                        </a>
                                        <br><br>


              You can also copy and paste this link into your browser:<br><br>

              https://admin.bartumenergy.com/payment-plan/{{{$data['link']}}}

  <br>

  
  </div>



</body>

</html>
