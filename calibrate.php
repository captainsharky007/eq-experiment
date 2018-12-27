<?php
$info = pathinfo($_FILES['userFile']['name']);
$ext = $info['extension']; // get the extension of the file
$randomString = generateRandomString();
$newname = "calibrated.".$ext;
$uploadFolder = "uploads/".$randomString."/";
$target = $uploadFolder.$newname;
$original= $uploadFolder."original.".$ext;
mkdir('uploads/'.$randomString , 0777, true);
move_uploaded_file( $_FILES['userFile']['tmp_name'], $original);
  require 'vendor/autoload.php';
  $ffmpeg = FFMpeg\FFMpeg::create(
    array(
      'ffmpeg.binaries'  => 'E:/audioLibraries/ffmpeg/bin/ffmpeg.exe',
      'ffprobe.binaries' => 'E:/audioLibraries/ffmpeg/bin/ffprobe.exe',
      'timeout' => 3600, // The timeout for the underlying process
      'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
  ));

  //ffprobe -v error -show_entries stream=bit_rate -of default=noprint_wrappers=1:nokey=1 Original.mp3
  //ffprobe -v error -show_entries streams "Beats Solo2.mp3"
  $track = $uploadFolder."original.".$ext;

  // get bitrate
  $ffprobe = FFMpeg\FFProbe::create();
  $audio_properties = $ffprobe->streams($track)->audios()->first();
  $originalBitRate = $audio_properties->get('bit_rate') / 1000;

  $audio = $ffmpeg->open($track);
  $format = new FFMpeg\Format\Audio\Mp3();
  $format->setAudioKiloBitrate($originalBitRate);
  // $audio->filters()->addMetadata(["artwork" => "cover.jpg"]);
  // $audio->filters()->addMetadata(["title" => "Jevgenijs Zaremba"]);
  // $audio->filters()->addMetadata();
  $audio->filters()->eq("firequalizer=gain_entry='entry(20.00, 1.86);entry(21.47, 1.64);entry(23.04, 1.31); entry(24.73, 0.99); entry(26.54, 0.70); entry(28.49, 0.43); entry(30.57, 0.19); entry(32.82, -0.06); entry(35.22, -0.40); entry(37.80, -0.82); entry(40.57, -1.30); entry(43.55, -1.77); entry(46.74, -2.38); entry(50.17, -3.13); entry(53.84, -3.50); entry(57.79, -3.50); entry(62.03, -3.32); entry(66.57, -3.29); entry(71.45, -3.64); entry(76.69, -3.92); entry(82.31, -4.09); entry(88.34, -4.42); entry(94.82, -4.73); entry(101.77, -4.87); entry(109.23, -4.92); entry(117.24, -4.91); entry(125.83, -4.84); entry(135.05, -4.72); entry(144.95, -4.55); entry(155.58, -4.32); entry(166.98, -4.09); entry(179.22, -3.88); entry(192.36, -3.64); entry(206.46, -3.43); entry(221.59, -3.20); entry(237.83, -2.94); entry(255.26, -2.54); entry(273.98, -2.06); entry(294.06, -1.56); entry(315.61, -1.05); entry(338.75, -0.48); entry(363.58, 0.07); entry(390.23, 0.61); entry(418.83, 1.05); entry(449.53, 1.40); entry(482.48, 1.57); entry(517.85, 1.75); entry(555.81, 2.02); entry(596.55, 2.44); entry(640.27, 2.97); entry(687.21, 3.27); entry(737.58, 3.56); entry(791.64, 3.95); entry(849.67, 3.96); entry(911.95, 3.83); entry(978.80, 3.18); entry(1050.54, 2.00); entry(1127.55, 1.39); entry(1210.20, 1.08); entry(1298.90, 0.99); entry(1394.11, 1.03); entry(1496.30, 1.17); entry(1605.98, 1.56); entry(1723.70, 2.12); entry(1850.05, 2.86); entry(1985.66, 3.62); entry(2131.20, 4.08); entry(2287.42, 4.22); entry(2455.09, 4.09); entry(2635.05, 3.72); entry(2828.20, 3.44); entry(3035.50, 2.85); entry(3258.01, 2.04); entry(3496.82, 1.53); entry(3753.14, 1.35); entry(4028.24, 1.47); entry(4323.51, 1.61); entry(4640.42, 0.67); entry(4980.57, 0.22); entry(5345.64, 0.77); entry(5737.48, 0.56); entry(6158.04, 0.30); entry(6609.42, 1.05); entry(7093.89, 2.21); entry(7613.87, 3.04); entry(8171.97, 1.85); entry(8770.98, 0.55); entry(9413.89, 2.17); entry(10103.93, 2.39); entry(10844.54, 2.54); entry(11639.45, 3.19); entry(12492.62, 3.92); entry(13408.33, 2.71); entry(14391.16, 0.90); entry(15446.03, 2.21); entry(16578.23, 3.69); entry(17793.41, 2.72); entry(19097.67, 1.38); entry(20497.53, 5.23); entry(22000.00, 14.26)'");
  $audio->save($format, $target);

  function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
<div id="orig">
  <h5>Original:</h5>
  <audio controls id="original">
  <source src="<?php echo $original ?>" type="audio/mpeg">
  </audio>
</div>
<div id="cal" class="hide">
  <h5>Calibrated:</h5>
    <audio controls id="calibrated">
    <source src="<?php echo $target ?>" type="audio/mpeg">
  </audio>
</div>

<button id="btn" type="button" name="button" class="off" onclick="changeCalibration()">Calibration OFF</button>
<style media="screen">
  .on {
  background:green;
}
.off {
  background:orange;
}
.hide { display: none; }
.show { display: block; }
</style>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script type="text/javascript">
  var calibration = false;
  var orig = document.getElementById("orig");
  var cal = document.getElementById("cal");
  var playerOriginal = document.getElementById("original");
  var playerCalibrated = document.getElementById("calibrated");
  //player.addEventListener("seeked", function() { console.log("seeked...") }, true);
  function changeCalibration() {
    if(calibration) {
      calibration = !calibration;
      playerOriginal.currentTime = playerCalibrated.currentTime;
      playerOriginal.play();
      playerCalibrated.pause();
      $('#cal').toggle();
      $('#orig').toggle();
      $('#btn').addClass('off').removeClass('on').text('Calibration OFF');
    } else {
      calibration = !calibration;
      playerCalibrated.currentTime = playerOriginal.currentTime;
      playerCalibrated.play();
      playerOriginal.pause();
      $('#cal').toggle();
      $('#orig').toggle();
      $('#btn').addClass('on').removeClass('off').text('Calibration ON');
    }
  }
</script>
