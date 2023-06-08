<?php
$base_api = 'http://api.themoviedb.org/3/';
$curl = curl_init();

$curl_base = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwODRmODk4M2NkNjk1ZTU2NmJjY2M1MzRmY2Q1MTI2YiIsInN1YiI6IjY0ODAzMTBmZDJiMjA5MDEyZGZhZmUwYSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.drTR_Z8z04rcU5EnZKx51THEKx99VtyqDg5hhET9dKA",
        "accept: application/json"
    ],
];

function get($url, $params = [])
{
    global $curl, $curl_base;
    $curl_base[CURLOPT_URL] = $url . '?' . http_build_query($params);
    curl_setopt_array($curl, $curl_base);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
        return '';
    } else {
        return json_decode($response, true);
    }
}
if (isset($_GET['q']) && $_GET['q'] != '') {
    $moviesArray = get($base_api . 'search/movie', [
        'query' => $_GET['q'],
        'page' => 1,
        'language' => 'fr-FR',
    ])['results'];
} else {
$moviesArray = get($base_api . 'movie/now_playing', [
    'page' => 1,
    'language' => 'fr-FR',
])['results'];
}

$resultNumber = count($moviesArray);

//Create array of 3 movies for each carousel item
$moviesArray = array_chunk($moviesArray, 4);


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<style>
    .card-text {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* number of lines to show */
        line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .card-title {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 1; /* number of lines to show */
        line-clamp: 1;
        -webkit-box-orient: vertical;
    }

    .container {
        width: 70%;
    }

    .img-fluid {
        height: 300px;
        object-fit: cover;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                </li>
            </ul>
            <form class="d-flex">
                <input name="q" class="form-control me-2" type="search" placeholder="Search" aria-label="Search" value="<?php echo isset($_GET['q']) ? $_GET['q'] : '' ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
<div id="carouselExampleCaptions" class="container carousel carousel-dark slide" data-bs-ride="false">
    <div class="carousel-inner">
        <h1 class="text-center my-5">
            <i class="bi bi-film"></i>
            <?php if (isset($_GET['q']) && $_GET['q'] != ''): ?>
                Search results for "<?php echo $_GET['q'] ?>" : <?php echo $resultNumber ?> results
            <?php else: ?>
                Now Playing
            <?php endif; ?>
        </h1>
        <?php
        //create carousel item for each 3 movies
        $i = 0;
        foreach ($moviesArray as $carousel) {
            $active = $i === 0 ? 'active' : '';
            echo "<div class='carousel-item $active'>";
            echo "<div class='row'>";
            //Create card for each movie
            foreach ($carousel as $movie) {
                ?>
                <div class='col-3 d-flex'>
                    <div class='card'>
                        <img src='https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>' class='card-img-top img-fluid' onerror="this.src='https://picsum.photos/200/300'" alt='...'>
                        <div class='card-body d-flex flex-column justify-content-around'>
                            <h5 class='card-title'><?= $movie['title'] ?></h5>
                            <div class='mt-auto d-flex flex-column justify-content-between'>
                                <p class='card-text'><?= $movie['overview'] ?></p>
                                <button href='#' class='btn btn-primary'>Go somewhere</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php

            }
            echo "</div>";
            echo "</div>";
            $i++;
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
</body>
</html>
