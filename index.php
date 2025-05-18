<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['date_m'];
   $check_in = filter_var($date_m, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE date_m = ?");
   $check_bookings->execute([$date_m]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_stadium += $fetch_bookings['stadium'];
   }

   // if the hotel has total 30 rooms 
   if($total_stadium >= 6){
      $warning_msg[] = 'stadium are not available';
   }else{
      $success_msg[] = 'stadium are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['stadium'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['date_m'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['time_m'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['players'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['out_players'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE date_m = ?");
   $check_bookings->execute([$date_m]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_stadium += $fetch_bookings['stadium'];
   }

   if($total_stadium >= 6){
      $warning_msg[] = 'stadium are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND stadium = ? AND date_m = ? AND time_m = ? AND players = ? AND out_players = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $stadium, $date_m, $time_m, $players, $out_players]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'stadium booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, stadium, date_m, time_m, players, out_players) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $stadium, $date_m, $time_m, $players, $out_players]);
         $success_msg[] = 'stadium booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/img1.jpeg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#availability" class="btn">check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/img2.jpg" alt="">
            <div class="flex">
               <h3>foods and drinks</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/img3.jpg" alt="">
            <div class="flex">
               <h3>luxurious halls</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>date <span>*</span></p>
            <input type="date" name="date_m" class="input" required>
         </div>
         <div class="box">
            <p>time <span>*</span></p>
            <input type="time" name="time_m" class="input" required>
         </div>
         <div class="box">
            <p>player  <span>*</span></p>
            <select name="players" class="input" required>
               <option value="1">5 player</option>
               <option value="2">6 player</option>
               <option value="3">7 player</option>
            </select>
         </div>
         <div class="box">
            <p>out player <span>*</span></p>
            <select name="out_players" class="input" required>
               <option value="-">0 player</option>
               <option value="1">1 player</option>
               <option value="2">2  players</option>
               <option value="3">3  players</option>
            </select>
         </div>
         <div class="box">
            <p>stadium <span>*</span></p>
            <select name="stdium" class="input" required>
               <option value="1">1 stadium</option>
               <option value="2">2 stadium</option>
               <option value="3">3 stadium</option>
               <option value="4">4 stadium</option>
               <option value="5">5 stadium</option>
               <option value="6">6 stadium</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/i_img1.webp.jpeg" alt="">
      </div>
      <div class="content">
         <h3>Adidas Al Rihla Ball</h3>
         <p>Adidas Al Rihla is name of official match ball of FIFA World Cup 2022 in Qatar The World Cup ball 2022 has a shape of the 20 panels which is inspired by sand dunes. The inspiration of the color scheme of the Adidas Al Rihla official match ball is the "colors of the Qatari flag and traditionally white Arab clothes".</p>
         <a href="https://football-balls.com/ball-details/adidas-al-rihla-official-match-ball-of-world-cup-2022-in-qatar" class="btn">information
         </a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/i_img2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Standard dimensions of the mini football</h3>
         <p>Considering that the standard dimensions of the mini football terrain can be 20m x 40m, 25m x 45m, 30m x 50m and several others. The small football terrain is 50 m x 7 m and 9 m. With different proportions, 30 m x 50 m are more suitable.</p>
         <a href="https://fr.reformsports.com/caracteristiques-et-dimensions-des-mini-terrains-de-football/" class="btn">information
         </a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/i_img3.jpg.webp" alt="">
      </div>
      <div class="content">
         <h3>Best Artificial Grass</h3>
         <p>Artificial Grass 10mm – Synthetic Lawn – Roll of Fake Grass for Indoor and Outdoor Use</p>
         <a href="https://www.wlgrass.com/products/25mm-and-30mm-unfilled-football-grass.htmly" class="btn">information
         </a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/ic_1.png" alt="">
         <h3>Referee</h3>
         <p>The referee makes crucial decisions to ensure fair play.</p>
      </div>

      <div class="box">
         <img src="images/ic_2.png" alt="">
         <h3>Player</h3>
         <p>The player controls the ball with passion and determination.</p>
      </div>

      <div class="box">
         <img src="images/ic_3.png" alt="">
         <h3>Red and Yellow Cards</h3>
         <p>Red and yellow cards are used to penalize fouls based on their severity.</p>
      </div>

      <div class="box">
         <img src="images/ic_4.png" alt="">
         <h3>Trophy</h3>
         <p>The trophy rewards the winning team after a season of effort.</p>
      </div>

      <div class="box">
         <img src="images/ic_5.png" alt="">
         <h3>Goalkeeper</h3>
         <p>The goalkeeper defends the goal with reflexes and courage.</p>
      </div>

      <div class="box">
         <img src="images/ic_6.png" alt="">
         <h3>Kit</h3>
         <p>The player's kit includes the jersey, shorts, socks, and cleats.</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>make a reservation</h3>
      <div class="flex">
         <div class="box">
            <p>your name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>your email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>your number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>stadium <span>*</span></p>
            <select name="stadium" class="input" required>
               <option value="1" selected>1 stadium</option>
               <option value="2">2 stadium</option>
               <option value="3">3 stadium</option>
               <option value="4">4 stadium</option>
               <option value="5">5 stadium</option>
               <option value="6">6 stadium</option>
            </select>
         </div>
         <div class="box">
            <p>date <span>*</span></p>
            <input type="date" name="date_m" class="input" required>
         </div>
         <div class="box">
            <p>time <span>*</span></p>
            <input type="time" name="time_m" class="input" required>
         </div>
         <div class="box">
            <p>players <span>*</span></p>
            <select name="players" class="input" required>
               <option value="1" selected>5 players</option>
               <option value="2">5 players</option>
               <option value="3">6 players</option>
               <option value="4">7 players</option>
            </select>
         </div>
         <div class="box">
            <p>out players <span>*</span></p>
            <select name="out_players" class="input" required>
               <option value="0" selected>0 out player</option>
               <option value="1">1 out players</option>
               <option value="2">2 out players</option>
               <option value="3">3 out players</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/h_img1.jpg" class="swiper-slide" alt="">
         <img src="images/h_img2.jpeg" class="swiper-slide" alt="">
         <img src="images/h_img3.jpg" class="swiper-slide" alt="">
         <img src="images/h_img4.jpg" class="swiper-slide" alt="">
         <img src="images/h_img5.jpg" class="swiper-slide" alt="">
         <img src="images/h_img6.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">frequently asked questions</h3>
         <div class="box active">
            <h3>how to cancel?</h3>
            <p>You can cancel your reservation by logging into your account, going to “My Bookings,” and clicking the “Cancel” button next to the reservation you wish to cancel. Please note that cancellation policies may apply depending on the time and service..</p>
         </div>
         <div class="box">
            <h3>is there any vacancy?</h3>
            <p>Yes, availability depends on the date and service selected. We recommend checking the availability calendar on our website or contacting our support team for up-to-date information.</p>
         </div>
         <div class="box">
            <h3>what are payment methods?</h3>
            <p>you can pay directly at our office or service point using cash, credit/debit cards, or mobile payment apps like Apple Pay or Google Pay</p>
         </div>
         <div class="box">
            <h3>how to claim coupons codes?</h3>
            <p>To claim a coupon code, enter it in the designated field at checkout before completing your payment. If the code is valid, the discount will be applied automatically. Make sure the coupon is not expired and meets all conditions.</p>
         </div>
         <div class="box">
            <h3>what are the age requirements?</h3>
            <p>Our services are open to all ages. However, users under 18 may need parental guidance or consent depending on the type of service. Please check the specific service details for more information.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/hamdi.jpg" alt="">
            <h3>El nahali Hamdi</h3>
            <p>devloper</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>Araab Ismahen</h3>
            <p>devloper</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>