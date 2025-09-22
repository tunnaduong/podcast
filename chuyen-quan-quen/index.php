<?php
$episodes = json_decode(file_get_contents('data.json'), true);
// Reverse the episodes array to show newest first
$episodes = array_reverse($episodes);
$domain = "https://radio.tunnaduong.com/chuyen-quan-quen";

// Function to check if request is for XML feed
function isXmlRequest()
{
    // Check explicit format parameter first
    if (isset($_GET['format']) && $_GET['format'] === 'xml') {
        return true;
    }

    // Get Accept header and User Agent
    $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    // Known podcast clients and feed readers
    $feedReaders = ['feedly', 'rssreader', 'podcasts/', 'itunes', 'overcast'];
    foreach ($feedReaders as $reader) {
        if (stripos($userAgent, $reader) !== false) {
            return true;
        }
    }

    // If it looks like a browser request, serve HTML
    if (
        stripos($userAgent, 'Mozilla') !== false ||
        stripos($userAgent, 'Chrome') !== false ||
        stripos($userAgent, 'Safari') !== false ||
        stripos($userAgent, 'Edge') !== false ||
        stripos($userAgent, 'Firefox') !== false
    ) {
        return false;
    }

    // For non-browser requests, check if XML is explicitly requested
    $xmlTypes = ['application/rss+xml', 'application/xml', 'text/xml'];
    foreach ($xmlTypes as $type) {
        if (stripos($acceptHeader, $type) !== false) {
            return true;
        }
    }

    // Default to HTML
    return false;
}

// For debugging
if (isset($_GET['debug'])) {
    header('Content-Type: text/plain');
    echo "Accept header: " . (isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'not set') . "\n";
    echo "User Agent: " . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'not set') . "\n";
    echo "Will serve: " . (isXmlRequest() ? 'XML' : 'HTML') . "\n";
    exit;
}

if (isXmlRequest()) {
    header("Content-Type: application/rss+xml; charset=utf-8");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ?>
    <rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
        <channel>
            <title>Chuyện quán quen</title>
            <link><?= $domain ?></link>
            <description>Podcast chia sẻ câu chuyện về cuộc sống giữa ba con người trong một quán quen.</description>
            <language>vi-VN</language>
            <itunes:author>Tunna Duong</itunes:author>
            <itunes:owner>
                <itunes:name>Tunna Duong</itunes:name>
                <itunes:email>tunnaduong@gmail.com</itunes:email>
            </itunes:owner>
            <itunes:image href="<?= $domain ?>/cover.jpg" />
            <itunes:category text="Leisure">
                <itunes:category text="Relationships" />
            </itunes:category>

            <?php foreach ($episodes as $ep): ?>
                <item>
                    <title><?= htmlspecialchars($ep['title']) ?></title>
                    <description><?= htmlspecialchars($ep['description']) ?></description>
                    <enclosure url="<?= $domain . $ep['audio'] ?>" type="audio/mpeg" />
                    <guid><?= $domain . $ep['audio'] ?></guid>
                    <pubDate><?= $ep['pubDate'] ?></pubDate>
                    <itunes:duration><?= $ep['duration'] ?></itunes:duration>
                    <itunes:image href="<?= $domain . $ep['thumbnail'] ?>" />
                </item>
            <?php endforeach; ?>
        </channel>
    </rss>
    <?php
} else {
    // Serve HTML content
    header("Content-Type: text/html; charset=utf-8");
    ?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chuyện quán quen - Podcast by Tunna Duong</title>
        <meta name="description"
            content="Podcast chia sẻ câu chuyện về cuộc sống giữa ba con người trong một quán quen. Những cuộc trò chuyện chân thật về tình yêu, tình bạn và những trải nghiệm trong cuộc sống." />
        <meta property="og:title" content="Chuyện quán quen - Podcast by Tunna Duong" />
        <meta property="og:description"
            content="Podcast chia sẻ câu chuyện về cuộc sống giữa ba con người trong một quán quen. Những cuộc trò chuyện chân thật về tình yêu, tình bạn và những trải nghiệm trong cuộc sống." />
        <meta property="og:image" content="<?= $domain ?>/cover.jpg" />
        <meta property="og:url" content="<?= $domain ?>" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Chuyện quán quen - Podcast by Tunna Duong" />
        <meta name="twitter:description"
            content="Podcast chia sẻ câu chuyện về cuộc sống giữa ba con người trong một quán quen." />
        <meta name="twitter:image" content="<?= $domain ?>/cover.jpg" />
        <link rel="icon" type="image/png" href="/t_radio.png" />
        <link rel="apple-touch-icon" href="/t_radio.png" />
        <!-- Add RSS feed discovery link -->
        <link rel="alternate" type="application/rss+xml" title="Chuyện quán quen RSS Feed" href="<?= $domain ?>?format=xml">
        <style>
            body {
                font-family: system-ui, -apple-system, sans-serif;
                line-height: 1.6;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background: #f5f5f5;
            }

            h1 {
                margin: 0;
                text-align: center;
            }

            p {
                margin-top: 0;
            }

            header {
                text-align: center;
                margin-bottom: 40px;
            }

            .cover-image {
                width: 300px;
                height: 300px;
                border-radius: 10px;
                margin: 20px 0;
            }

            .episode {
                background: white;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .episode h2 {
                margin-top: 0;
                color: #333;
            }

            .episode-meta {
                color: #666;
                font-size: 0.9em;
                margin: 10px 0;
            }

            audio {
                width: 100%;
                margin: 10px 0;
            }

            .xml-link {
                text-align: center;
                margin-top: 40px;
                padding: 20px;
                background: #eee;
                border-radius: 5px;
            }

            .xml-link a {
                color: #666;
                text-decoration: none;
            }

            .xml-link a:hover {
                text-decoration: underline;
            }
        </style>
    </head>

    <body>
        <a href="/" style="
          text-decoration: none;
          display: flex;
          align-items: center;
          justify-content: center;
          color: black;
          margin: 0 auto;
        ">
            <img src="/t_radio.png" alt="Tunna Radio" class="logo" style="
            width: 100px;
            height: 100px;
            object-fit: contain;
            display: block;
          " />
            <h1>Tunna Radio</h1>
        </a>
        <header>
            <img src="/chuyen-quan-quen/cover.jpg" alt="Chuyện quán quen Cover" class="cover-image">
            <h1>Chuyện quán quen</h1>
            <p>Podcast chia sẻ câu chuyện về cuộc sống giữa ba con người trong một quán quen.</p>
            <div style="margin: 20px 0;">
                <a href="https://open.spotify.com/show/1dj88aG97PLqU6A0HkWlqD" target="_blank"
                    style="margin: 0 5px; text-decoration: none;">
                    <img src="/badges/spotify.png" alt="Listen on Spotify" style="height: 40px;">
                </a>
                <a target="_blank" style="margin: 0 5px; text-decoration: none;"
                    href="https://podcasts.apple.com/us/podcast/chuy%E1%BB%87n-qu%C3%A1n-quen/id1838320680?itscg=30200&itsct=podcast_box_badge&ls=1&mttnsubad=1838320680">
                    <img src="/badges/apple-podcasts.png" alt="Listen on Apple Podcasts" style="height: 40px;" />
                </a>
                <a href="https://www.youtube.com/playlist?list=PLBPqAGxScH1ssyUreF3YN-xfNAdz8Dr0G" target="_blank"
                    style="margin: 0 5px; text-decoration: none;">
                    <img src="/badges/youtube.png" alt="Listen on YouTube" style="height: 40px;">
                </a>
                <a href="<?= $domain ?>?format=xml" target="_blank" style="margin: 0 5px; text-decoration: none;">
                    <img src="/badges/rss.png" alt="Listen on RSS" style="height: 40px;">
                </a>
            </div>
        </header>

        <main>
            <?php foreach ($episodes as $ep): ?>
                <article class="episode">
                    <h2><?= htmlspecialchars($ep['title']) ?></h2>
                    <div class="episode-meta">
                        <time datetime="<?= date('c', strtotime($ep['pubDate'])) ?>">
                            <?= date('d/m/Y', strtotime($ep['pubDate'])) ?>
                        </time>
                        • <?= $ep['duration'] ?>
                    </div>
                    <p><?= nl2br(htmlspecialchars($ep['description'])) ?></p>
                    <audio controls>
                        <source src="/chuyen-quan-quen<?= $ep['audio'] ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </article>
            <?php endforeach; ?>
        </main>

        <div class="xml-link">
            <a href="?format=xml">XML Feed cho Podcast Players</a><br>
            <a href="https://tunnaduong.com/">&copy; 2025 Tunna Duong</a>
        </div>
    </body>

    </html>
    <?php
}
?>