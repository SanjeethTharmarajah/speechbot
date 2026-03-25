<?php
header("Content-Type: application/json");

require_once 'config.php'; // Ensure this contains your Groq API Key

// Get user input
$input = json_decode(file_get_contents("php://input"), true);
$userText = $input["text"] ?? '';

if (empty($userText)) {
    echo json_encode(["response" => "No input text received."]);
    exit;
}

// Set up cURL for Groq API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . GROQ_API_KEY,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // Force IPv4
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout
curl_setopt($ch, CURLOPT_POST, true);

// Prepare data for API request
$data = json_encode([
    "model" => "llama-3.3-70b-versatile",  // ✅ Use this instead of "llama3-8b"
    "messages" => [
        ["role" => "system", "content" => "You are a helpful AI assistant."],
        ["role" => "user", "content" => $userText]
    ],
    "temperature" => 0.7,
    "max_tokens" => 150
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// Execute API request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Log API response for debugging
file_put_contents("groq_api_log.txt", "User Input: " . $userText . "\nResponse: " . $response . "\n\n", FILE_APPEND);

// Check for cURL errors
if ($error) {
    echo json_encode(["response" => "cURL Error: $error"]);
    exit;
}

// Decode response
$responseData = json_decode($response, true);

// Handle invalid JSON response
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["response" => "Error decoding response from Groq API."]);
    exit;
}

// Check API response structure
if ($httpCode !== 200 || !isset($responseData["choices"][0]["message"]["content"])) {
    echo json_encode(["response" => "I'm sorry, I couldn't process that."]);
    exit;
}

// Extract and return AI response
$chatResponse = $responseData["choices"][0]["message"]["content"];
echo json_encode(["response" => trim($chatResponse)]);
?>
