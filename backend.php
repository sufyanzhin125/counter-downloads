<?php
header('Content-Type: application/json');

$url = $_POST['url'] ?? '';
$quality = $_POST['quality'] ?? '720';

if(empty($url)) {
    echo json_encode(['success' => false, 'error' => 'No URL provided']);
    exit;
}

// Extract video ID
preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?#]+)/', $url, $matches);
if(empty($matches[1])) {
    echo json_encode(['success' => false, 'error' => 'Invalid YouTube URL']);
    exit;
}

$videoId = $matches[1];

// Use a working public YouTube download API
$apiUrls = [
    "https://ytdlapi.com/api/download?url=https://www.youtube.com/watch?v={$videoId}&quality={$quality}",
    "https://api.ytdlp.com/video/info?url=https://www.youtube.com/watch?v={$videoId}",
    "https://youtube-mp3-downloader2.p.rapidapi.com/ytdl/video?id={$videoId}"
];

$downloadUrl = null;
$title = "YouTube Video";

foreach($apiUrls as $apiUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($httpCode == 200 && $response) {
        $data = json_decode($response, true);
        if($data) {
            if(isset($data['downloadUrl']) || isset($data['url'])) {
                $downloadUrl = $data['downloadUrl'] ?? $data['url'];
                $title = $data['title'] ?? "YouTube Video";
                break;
            }
            if(isset($data['link'])) {
                $downloadUrl = $data['link'];
                $title = $data['title'] ?? "YouTube Video";
                break;
            }
        }
    }
}

// Fallback: Use yt-dlp via external service
if(!$downloadUrl) {
    $fallbackUrl = "https://youtube-video-download-info.p.rapidapi.com/dl?id={$videoId}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fallbackUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    if($data && isset($data['download']['url'])) {
        $downloadUrl = $data['download']['url'];
        $title = $data['title'] ?? "YouTube Video";
    }
}

if($downloadUrl) {
    echo json_encode([
        'success' => true,
        'title' => $title,
        'downloadUrl' => $downloadUrl
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Could not fetch video. Try another quality or video.'
    ]);
}
?>
