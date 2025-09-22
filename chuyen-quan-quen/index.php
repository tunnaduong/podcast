<?php
header("Content-Type: application/rss+xml; charset=utf-8");

$episodes = json_decode(file_get_contents('data.json'), true);
$domain = "https://radio.tunnaduong.com/chuyen-quan-quen";

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