<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['url'])) {
    echo json_encode(['error' => 'No URL provided']);
    exit;
}

$url = $_POST['url'];

// Use yt-dlp executable (you'll install on server)
// For free hosting that allows exec() like some shared hosting or your own VPS
// Alternatively, use public API as fallback — but here's the REAL DEAL:

// Method 1: If you have yt-dlp installed on server
$output = shell_exec("yt-dlp -j --cookies cookies.txt " . escapeshellarg($url) . " 2>&1");

if (!$output) {
    // Fallback to free public API (ytdlp-API mirror)
    $apiUrl = "https://ytdlp-api.vercel.app/api/info?url=" . urlencode($url);
    $context = stream_context_create(['http' => ['timeout' => 15]]);
    $output = @file_get_contents($apiUrl, false, $context);
}

if (!$output) {
    echo json_encode(['error' => 'Failed to fetch video data. Try again.']);
    exit;
}

$data = json_decode($output, true);
if (!$data || isset($data['error'])) {
    echo json_encode(['error' => 'Invalid response from downloader']);
    exit;
}

// Build formats array
$formats = [];
if (isset($data['formats'])) {
    foreach ($data['formats'] as $f) {
        if (in_array($f['ext'], ['mp4', 'webm']) && isset($f['url'])) {
            $formats[] = [
                'quality' => $f['quality'] ?? ($f['height'] ? $f['height'] . 'p' : 'Unknown'),
                'type' => $f['ext'],
                'url' => $f['url']
            ];
        }
    }
}

// If no formats, fallback to direct best
if (empty($formats) && isset($data['url'])) {
    $formats[] = [
        'quality' => 'Best',
        'type' => 'mp4',
        'url' => $data['url']
    ];
}

echo json_encode([
    'title' => $data['title'] ?? 'Unknown Title',
    'length' => $data['duration'] ? gmdate("i:s", $data['duration']) : 'N/A',
    'formats' => $formats
]);
?>
