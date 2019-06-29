<?php
include_once("config.php");
if($_ENV["DS_SECRET"] == ""){
    die("No DarkSky API Secret");
}

include_once("class.comms.php");

$apiComms = new comms;
$getWeatherData = null;
$getPostcodeData = null;

//Location form posted
if(isset($_POST["submit_location"])){
    $postcode = $_POST["postcode"];
    if($postcode){
        $requestUrl = "http://api.postcodes.io/postcodes/".$postcode;
        $getPostcodeData = $apiComms->get($requestUrl);
        $getPostcodeData = json_decode($getPostcodeData);
        if($getPostcodeData->status == 200){
            $lat = $getPostcodeData->result->latitude;
            $lon = $getPostcodeData->result->longitude;

            $requestUrl = "https://api.darksky.net/forecast/".$_ENV["DS_SECRET"]."/".$lat.",".$lon;
            $getWeatherData = json_decode($apiComms->get($requestUrl));
            // $getWeatherData = json_decode($apiComms->get("http://isitmiataweather.local/test.json"));
        }
    }
}


// echo "<pre>";
// print_r($getWeatherData->daily);
// echo "</pre>";

function returnOutcome($precipProbability){
    $outcome = "";
    if($precipProbability < 0.05) {
        $outcome = "Yes! 😎";
    } else if($precipProbability >= 0.05 && $precipProbability < 0.1){
        $outcome = "Maybe 🤔";
    } else {
        $outcome = "Nope 😔";
    }

    return $outcome;
}

?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>Is it Miata weather?</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" href="icon.png">

    <link rel="stylesheet" href="assets/css/main.css">

    <meta name="theme-color" content="#000000">
</head>
<body>
    <main>
        <div class="container">
            <div class="your-location">
                <form method="POST">
                    <label>Your postcode...</label>
                    <input type="text" name="postcode" value="<?php echo (isset($getPostcodeData->result->postcode)) ? $getPostcodeData->result->postcode : null; ?>" required/>
                    <input type="submit" name="submit_location" value="Get forecast"/>
                </form>
            </div>

            <?php if($getWeatherData != null): ?>
                <div class="today">
                    <h1>Is it Miata weather today?</h1>
                    <h2><?= returnOutcome($getWeatherData->daily->data[0]->precipProbability); ?></h2>
                </div>

                <div class="tomorrow">
                    <h1>How about tomorrow?</h1>
                    <h2><?= returnOutcome($getWeatherData->daily->data[1]->precipProbability); ?></h2>
                </div>

                <div class="forecast">
                    <h1>The Miata forecast</h1>
                    <div>
                        <h3>Today</h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[0]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[1]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[1]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[2]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[2]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[3]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[3]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[4]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[4]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[5]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[5]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[6]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[6]->precipProbability); ?></span>
                    </div>
                    <div>
                        <h3><?= date("D", $getWeatherData->daily->data[7]->time); ?></h3>
                        <span><?= returnOutcome($getWeatherData->daily->data[7]->precipProbability); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="today">
                    <h2>Enter a postcode to find out the Miata forecast!</h2>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="container">
        <p>Made by <a href="https://aaronfisher.net">Aaron Fisher</a> and <a href="https://darksky.net/poweredby/" target="_blank">powered by Dark Sky</a></p>
    </footer>
</body>
</html>