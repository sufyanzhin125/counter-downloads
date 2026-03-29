<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counter Downloads | YouTube Downloader</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', monospace;
        }
        body {
            background: radial-gradient(circle at 20% 30%, #0a0f1e, #000000);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .card {
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(10px);
            border: 1px solid #00ffcc;
            border-radius: 2rem;
            padding: 1.5rem;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 0 40px #00ffcc33;
        }
        h1 {
            text-align: center;
            color: #00ffcc;
            font-size: 1.6rem;
            text-shadow: 0 0 5px #00ffcc;
        }
        .counter {
            text-align: center;
            background: #111;
            padding: 0.6rem;
            border-radius: 2rem;
            margin: 1rem 0;
            color: #ffaa00;
            font-weight: bold;
        }
        .ad-box {
            background: #ff00aa22;
            border: 2px solid #ff00aa;
            padding: 0.8rem;
            border-radius: 1rem;
            text-align: center;
            cursor: pointer;
            margin: 1rem 0;
            color: #ff99cc;
            font-weight: bold;
            transition: 0.2s;
        }
        .ad-box:hover {
            background: #ff00aa44;
        }
        input {
            width: 100%;
            padding: 0.9rem;
            background: #111;
            border: 1px solid #00ffcc;
            border-radius: 2rem;
            color: white;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        button {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(90deg, #00ffcc, #ff00aa);
            border: none;
            border-radius: 2rem;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }
        .result {
            margin-top: 1.2rem;
            background: #000000aa;
            border-radius: 1rem;
            padding: 1rem;
            display: none;
        }
        #videoTitle {
            color: #0ff;
            margin-bottom: 0.8rem;
            word-break: break-word;
        }
        .download-links a {
            display: inline-block;
            background: #00ffcc22;
            padding: 0.5rem 1rem;
            margin: 0.3rem;
            border-radius: 2rem;
            color: #0ff;
            text-decoration: none;
            font-size: 0.85rem;
        }
        footer {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.7rem;
            color: #888;
        }
        .loading {
            color: #ffaa00;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>📀 COUNTER DOWNLOADS 📀</h1>
    <div class="counter" id="counterDisplay">📥 TOTAL DOWNLOADS: 0</div>
    
    <div class="ad-box" id="adSpot">
        🔥 CLICK HERE FIRST — SUPPORT THE SITE 🔥<br>
        (Ek baar click karo, phir unlimited download)
    </div>
    
    <input type="text" id="videoUrl" placeholder="YouTube link daalo...">
    <button id="downloadBtn">⬇️ DOWNLOAD NOW ⬇️</button>
    
    <div id="resultBox" class="result">
        <div id="videoTitle"></div>
        <div id="downloadLinks" class="download-links"></div>
    </div>
    <footer>⚡ First click = ad opens • No limits • Free ⚡</footer>
</div>

<script>
    let adClicked = false;
    let totalDownloads = localStorage.getItem('totalDownloads') ? parseInt(localStorage.getItem('totalDownloads')) : 0;
    document.getElementById('counterDisplay').innerHTML = '📥 TOTAL DOWNLOADS: ' + totalDownloads;
    
    // 🔥 YAHAN APNA AD LINK LAGA 🔥
    // Pehle shorte.st ya adfly pe account bana ke link bana
    // Phir niche wali line mein apna link daal
    const YOUR_AD_LINK = "https://www.shorte.st/your-link-here";
    
    document.getElementById('adSpot').onclick = function() {
        if(!adClicked) {
            window.open(YOUR_AD_LINK, '_blank');
            adClicked = true;
            this.style.opacity = '0.6';
            this.innerHTML = '✅ AD CLICKED! NOW DOWNLOAD ANY VIDEO ✅';
            // Save to localStorage so user doesn't need to click again
            localStorage.setItem('adClicked', 'true');
        }
    };
    
    // Check if user already clicked ad before
    if(localStorage.getItem('adClicked') === 'true') {
        adClicked = true;
        document.getElementById('adSpot').style.opacity = '0.6';
        document.getElementById('adSpot').innerHTML = '✅ AD ALREADY CLICKED! ✅<br>UNLIMITED DOWNLOADS ACTIVE';
    }
    
    document.getElementById('downloadBtn').onclick = async function() {
        let url = document.getElementById('videoUrl').value;
        if(!url) {
            alert('Pehle YouTube link daal!');
            return;
        }
        if(!adClicked) {
            alert('Pehle upar wale AD box pe click kar! (Sirf ek baar)');
            return;
        }
        
        let resultDiv = document.getElementById('resultBox');
        let titleDiv = document.getElementById('videoTitle');
        let linksDiv = document.getElementById('downloadLinks');
        resultDiv.style.display = 'block';
        titleDiv.innerHTML = '<div class="loading">🕶️ FETCHING VIDEO... 🕶️</div>';
        linksDiv.innerHTML = '';
        
        try {
            // Extract video ID from YouTube URL
            let videoId = extractVideoId(url);
            if(!videoId) {
                titleDiv.innerHTML = '❌ Invalid YouTube link!';
                return;
            }
            
            // Method 1: Try working YouTube download API
            let apiUrl = `https://pipedapi.kavin.rocks/streams/${videoId}`;
            
            let response = await fetch(apiUrl);
            let data = await response.json();
            
            if(data && data.videoStreams && data.videoStreams.length > 0) {
                // Get best quality video
                let bestQuality = data.videoStreams[data.videoStreams.length - 1];
                titleDiv.innerHTML = '🎬 ' + (data.title || 'Video') + ' 🎬';
                linksDiv.innerHTML = `<a href="${bestQuality.url}" download target="_blank">💾 DOWNLOAD VIDEO (${bestQuality.quality}p)</a>`;
                
                // Update counter
                totalDownloads++;
                localStorage.setItem('totalDownloads', totalDownloads);
                document.getElementById('counterDisplay').innerHTML = '📥 TOTAL DOWNLOADS: ' + totalDownloads;
            } else {
                titleDiv.innerHTML = '❌ No download links found. Try another video.';
            }
            
        } catch(e) {
            console.log(e);
            // Fallback method: Alternative API
            try {
                let fallbackUrl = `https://youtube-mp3-downloader2.p.rapidapi.com/ytdl/video?id=${videoId}`;
                let fallbackResponse = await fetch(fallbackUrl, {
                    headers: {
                        'x-rapidapi-key': 'demo-key-please-change',
                        'x-rapidapi-host': 'youtube-mp3-downloader2.p.rapidapi.com'
                    }
                });
                let fallbackData = await fallbackResponse.json();
                if(fallbackData && fallbackData.link) {
                    titleDiv.innerHTML = '🎬 ' + (fallbackData.title || 'Video') + ' 🎬';
                    linksDiv.innerHTML = `<a href="${fallbackData.link}" download target="_blank">💾 DOWNLOAD VIDEO</a>`;
                    
                    totalDownloads++;
                    localStorage.setItem('totalDownloads', totalDownloads);
                    document.getElementById('counterDisplay').innerHTML = '📥 TOTAL DOWNLOADS: ' + totalDownloads;
                } else {
                    titleDiv.innerHTML = '💀 ERROR: Try another video link 💀';
                }
            } catch(e2) {
                titleDiv.innerHTML = '💀 ERROR — TRY AGAIN OR CHECK LINK 💀';
            }
        }
    };
    
    function extractVideoId(url) {
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        let match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }
</script>
</body>
</html>
