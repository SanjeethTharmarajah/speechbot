const API_URL = "process_audio.php";  // Update with your backend URL
const botModel = document.getElementById("botModel");

// Function to send user input to backend AI
async function sendMessage() {
    let userText = document.getElementById("userInput").value;
    if (!userText.trim()) return;

    // Send request to AI backend
    let response = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ text: userText })
    });

    let data = await response.json();
    let botReply = data.response || "I couldn't process that.";

    speak(botReply);  // Convert text to speech
}

// Function to handle text-to-speech with lip sync
function speak(text) {
    let synth = window.speechSynthesis;
    let utterance = new SpeechSynthesisUtterance(text);
    
    utterance.onstart = () => botModel.setAttribute("animation-name", "Talking");
    utterance.onend = () => botModel.setAttribute("animation-name", "Idle");

    synth.speak(utterance);
}
