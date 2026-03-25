const API_URL = "text_to_speech.php";  // PHP backend URL
const canvas = document.getElementById("waveformCanvas");
const ctx = canvas.getContext("2d");
const audioContext = new (window.AudioContext || window.webkitAudioContext)();
let analyser = audioContext.createAnalyser();
analyser.fftSize = 256;
let bufferLength = analyser.frequencyBinCount;
let dataArray = new Uint8Array(bufferLength);

// Function to send user input and get AI response
async function sendMessage() {
    let userText = document.getElementById("userInput").value;
    if (!userText.trim()) return;

    // Send request to PHP backend
    let response = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `text=${encodeURIComponent(userText)}`
    });

    let data = await response.json();
    
    if (data.error) {
        console.error("Error:", data.error);
        alert("Error: " + data.error);
        return;
    }

    let audioUrl = data.audioUrl;  // Get generated audio file

    if (audioUrl) {
        playAudio(audioUrl);  // Play the response audio
    } else {
        alert("No audio response received.");
    }
}

// Function to play AI-generated speech and visualize waveform
function playAudio(audioUrl) {
    fetch(audioUrl)
        .then(response => response.arrayBuffer())
        .then(data => audioContext.decodeAudioData(data))
        .then(buffer => {
            const source = audioContext.createBufferSource();
            source.buffer = buffer;
            source.connect(analyser);
            analyser.connect(audioContext.destination);
            source.start();
            visualize();
        })
        .catch(error => console.error("Error loading audio:", error));
}

// Function to animate waveform
function visualize() {
    function draw() {
        requestAnimationFrame(draw);
        analyser.getByteTimeDomainData(dataArray);

        ctx.fillStyle = "black";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.lineWidth = 2;
        ctx.strokeStyle = "lime";

        ctx.beginPath();
        let sliceWidth = canvas.width / bufferLength;
        let x = 0;

        for (let i = 0; i < bufferLength; i++) {
            let v = dataArray[i] / 128.0;
            let y = v * canvas.height / 2;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
            x += sliceWidth;
        }

        ctx.lineTo(canvas.width, canvas.height / 2);
        ctx.stroke();
    }
    draw();
}
