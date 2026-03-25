<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$API_KEY = "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";  // Replace with your Groq API key
$API_URL = "https://api.groq.com/v1/chat/completions";  // Update API endpoint if needed

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text = $_POST["text"] ?? "";

    if (empty($text)) {
        echo json_encode(["error" => "No text provided"]);
        exit;
    }

    // Prepare data for Groq API
    $postData = json_encode([
        "model" => "mixtral-8x7b-32768",
        "messages" => [["role" => "user", "content" => $text]],
        "temperature" => 0.7
    ]);

    $ch = curl_init($API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $API_KEY"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo json_encode(["error" => "Failed to get AI response"]);
        exit;
    }

    $aiResponse = json_decode($response, true);
    $reply = $aiResponse["choices"][0]["message"]["content"] ?? "I'm sorry, I couldn't process that.";

    echo json_encode(["response" => $reply]);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
