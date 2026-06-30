<?php

namespace App\Controllers\Frontend;

class SeoController extends \Controller {
    public function sitemap(): void {
        $urls = [
            ['loc' => BASE_URL . '/', 'priority' => '1.0'],
            ['loc' => BASE_URL . '/about', 'priority' => '0.8'],
            ['loc' => BASE_URL . '/contact', 'priority' => '0.7'],
            ['loc' => BASE_URL . '/prayer', 'priority' => '0.7'],
            ['loc' => BASE_URL . '/join', 'priority' => '0.7'],
            ['loc' => BASE_URL . '/sermons', 'priority' => '0.8'],
            ['loc' => BASE_URL . '/events', 'priority' => '0.8'],
            ['loc' => BASE_URL . '/ministries', 'priority' => '0.8'],
            ['loc' => BASE_URL . '/blog', 'priority' => '0.7'],
            ['loc' => BASE_URL . '/gallery', 'priority' => '0.6'],
        ];

        foreach (\Database::fetchAll('SELECT slug, updated_at FROM sermons WHERE is_published = 1') as $row) {
            $urls[] = ['loc' => BASE_URL . '/sermons/' . $row['slug'], 'lastmod' => substr((string) $row['updated_at'], 0, 10), 'priority' => '0.6'];
        }
        foreach (\Database::fetchAll('SELECT slug, created_at FROM sermon_series WHERE is_active = 1') as $row) {
            $urls[] = ['loc' => BASE_URL . '/sermons/series/' . $row['slug'], 'lastmod' => substr((string) $row['created_at'], 0, 10), 'priority' => '0.6'];
        }
        foreach (\Database::fetchAll('SELECT slug, updated_at FROM events WHERE is_published = 1') as $row) {
            $urls[] = ['loc' => BASE_URL . '/events/' . $row['slug'], 'lastmod' => substr((string) $row['updated_at'], 0, 10), 'priority' => '0.7'];
        }
        foreach (\Database::fetchAll('SELECT slug, created_at FROM ministries WHERE is_active = 1') as $row) {
            $urls[] = ['loc' => BASE_URL . '/ministries/' . $row['slug'], 'lastmod' => substr((string) $row['created_at'], 0, 10), 'priority' => '0.6'];
        }
        foreach (\Database::fetchAll('SELECT slug, updated_at FROM blog_posts WHERE is_published = 1') as $row) {
            $urls[] = ['loc' => BASE_URL . '/blog/' . $row['slug'], 'lastmod' => substr((string) $row['updated_at'], 0, 10), 'priority' => '0.6'];
        }
        foreach (\Database::fetchAll('SELECT slug, created_at FROM gallery_albums WHERE is_published = 1') as $row) {
            $urls[] = ['loc' => BASE_URL . '/gallery/' . $row['slug'], 'lastmod' => substr((string) $row['created_at'], 0, 10), 'priority' => '0.5'];
        }

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1) . "</lastmod>\n";
            }
            echo '    <priority>' . htmlspecialchars($url['priority'], ENT_XML1) . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
    }

    public function robots(): void {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /portal\n";
        echo "Disallow: /app\n";
        echo "Sitemap: " . BASE_URL . "/sitemap.xml\n";
    }
}
