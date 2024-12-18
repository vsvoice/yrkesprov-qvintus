<?php  
include_once 'includes/header.php';

?>

<div class="container w-100 font-taviraj">
  <div class="mx-auto mw-1240 px-2 px-sm-4">
    <h1 class="my-5 font-taviraj">Kontakt</h1>
    <div class="d-flex flex-column">
      <p>Välkommen att kontakta oss på Qvintus Antikvariat!<br>
      Har du frågor om vårt sortiment, letar efter en specifik bok eller vill veta mer om våra tjänster? Hör gärna av dig via e-post, telefon eller besök oss direkt i butiken.</p>


        <p class="mt-4 mb-1"><span class="fw-semibold">E-post: </span>contactqvintus@gmail.com</p>
        <p class="my-1"><span class="fw-semibold">Telefon: </span>+358 485 5042 01</p>
        <p class="mt-1 mb-1"><span class="fw-semibold">Adress: </span></p>
        <p class="ps-2 mb-5">
          Qvintus Antikvariat rf<br>
          Gatuvägen 1<br>
          33100 Tammerfors<br>
          Finland
        </p>


      <div class='card bg-gold-beige p-4 p-sm-5 rounded-0 border-0 shadow position-relative font-taviraj'>
        <h2 class="mb-5 font-taviraj">Kontaktformulär</h2>
        <form action="" method="post" enctype="multipart/form-data" class="">

          <label class="form-label" for="name">Namn</label><br>
          <input class="form-control" type="text" name="name" id="name" required="required"><br>

          <label class="form-label" for="email">E-post</label><br>
          <input class="form-control" type="text" name="email" id="email"><br>

          <label class="form-label" for="description">Meddelande</label><br>
          <textarea class="form-control" name="description" id="description" rows="6" required="required"></textarea><br>

          <input class="btn btn-dark text-white py-2" type="submit" name="send-message-submit" value="Skicka">
      </div>
    </div>
  </div>
</div>

<?php
include_once 'includes/footer.php';
?>
