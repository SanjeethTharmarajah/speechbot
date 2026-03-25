<?php
// text_to_speech.php

require_once 'config.php';

$text = $_GET['text'] ?? 'Hello';
$text = urlencode($text);

// Google Text-to-Speech API URL
$google_tts_url = "https://translate.google.com/translate_tts?ie=UTF-8&q=$text&tl=en&client=tw-ob";

// Set headers to stream audio
header("Content-Type: audio/mpeg");
header("Cache-Control: no-cache");

// Stream the audio content directly to the user
$audioStream = fopen($google_tts_url, 'r');
if ($audioStream) {
    fpassthru($audioStream);
    fclose($audioStream);
} else {
    echo "Error retrieving audio.";
}
?>
