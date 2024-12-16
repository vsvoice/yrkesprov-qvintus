<?php  
include_once 'includes/header.php';

?>

<div class="container w-100 font-taviraj">
  <div class="mx-auto mw-1240">
    <h1 class="my-5 font-taviraj">Kontakt</h1>
    <div class="d-flex flex-column">
      <h2 class="mb-4">Har du önskemål? Frågor? Feedback?</h2> 
      <p>Kontakta oss gärna genom följande kanaler eller via formuläret nedan:</p>

      <div class='card p-4 d-flex rounded-0 border-0 shadow position-relative font-taviraj ms-3 mt-2 mb-5 me-auto'>
        <p class="lh-lg mb-0">
          <span class="fw-semibold">E-post: </span>contactqvintus@gmail.com<br>
          <span class="fw-semibold">Telefon: </span>+358 485 5042 01<br>
          <span class="fw-semibold">Postadress: </span>Gatuvägen 1 33100 Tammerfors Finland
        </p>
      </div>

      <div class='card bg-gold-beige p-5 rounded-0 border-0 shadow position-relative font-taviraj'>
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
