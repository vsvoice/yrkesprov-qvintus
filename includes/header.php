<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/class.user.php';
require_once 'includes/class.book.php';
require_once 'includes/config.php';
$user = new User($pdo);
$book = new Book($pdo);

if(isset($_GET['logout'])) {
	$user->logout();
}

$menuLinks = array(
    array(
        "title" => "Hem",
        "url" => "index.php"
		),
		array(
				"title" => "Exklusivt",
				"url" => "exclusives.php"
		),
		array(
				"title" => "Böcker",
				"url" => "books.php"
		),
		array(
				"title" => "Verksamhet",
				"url" => "about-us.php"
		),
		array(
				"title" => "Kontakt",
				"url" => "contact.php"
		)
	);
$userMenuLinks = array(
		array(
				"title" => "+ Ny bok",
				"url" => "newbook.php"
		),
		array(
				"title" => "+ Ny artikel",
				"url" => "newarticle.php"
		),
		array(
				"title" => "Alla produkter",
				"url" => "products.php"
		),
		array(
				"title" => "Mina produkter",
				"url" => "products.php?only-own=1&show-hidden=1"
		)
);
$adminMenuLinks = array(
    array(
        "title" => "Administratör",
        "url" => "admin.php"
		)
);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Qvintus</title>
	<link rel="stylesheet" href="css/style.css">
	<!--<script defer src="js/script.js"></script>-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<!--<link rel="icon" href="assets/Powerol.ico" type="image/ico">-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quattrocento+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Taviraj:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	
	<!-- Splide Core CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/splide.min.css">

	<!-- Splide JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/js/splide.min.js"></script>


</head>


<body>
<header class="container-fluid font-taviraj bg-dark px-0">
	<nav class="navbar navbar-expand-lg bg-gold-beige py-2 px-2">
	<div class="container-fluid mw-1240 px-2 px-sm-4">
		<div class="d-flex slign-items-center">
			<a class="me-2" href="index.php"><img src='assets/Qvintus_logo_black.svg' class='header-logo' alt='...'></a>
			<a class="navbar-brand d-flex align-items-center fs-4" href="index.php">Qvintus</a>
		</div>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
		<ul class="navbar-nav text-black gap-2" >
			<?php
			foreach ($menuLinks as $menuItem) {
				echo "<li class='nav-item'>
				<a class='nav-link btn btn-warning p-2 fw-normal text-reset' href='{$menuItem['url']}'>{$menuItem['title']}</a>
				</li>";
			}

			if(isset($_SESSION['user_id'])) {
				if ($user->checkUserRole(10)) {
						echo "<li class='nav-item dropdown'>
								<div class='nav-link btn btn-warning p-2 dropdown-toggle fw-normal text-reset' data-bs-toggle='dropdown' aria-expanded='false'>
									Innehåll
								</div>
								<ul class='dropdown-menu text-center text-lg-start'>";
							foreach ($userMenuLinks as $menuItem) {
								echo "<li><a class='dropdown-item' href='{$menuItem['url']}'>{$menuItem['title']}</a></li>";
							}
							if ($user->checkUserRole(50)) {
								echo "<li><a class='dropdown-item' href='attributes.php'>Hantera attribut</a></li>";
							}
							echo "</ul>
							</li>";
				}
				if ($user->checkUserRole(200)) {
					foreach ($adminMenuLinks as $menuItem) {
						echo "<li class='nav-item'>
						<a class='nav-link btn btn-warning p-2 fw-normal text-reset' href='{$menuItem['url']}'>{$menuItem['title']}</a>
						</li>";
					}
				}
				echo "
				<li class='nav-item'>
					<a class='nav-link btn btn-warning p-2 fw-normal text-reset' href='?logout=1.php'>Logga ut</a>
				</li>";
			}
			?>
		</ul>
		</div>
	</div>
	</nav>

</header>