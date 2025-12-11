<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        // Get the sitemap content
        $sitemapPath = public_path('sitemap.xml');
        
        if (file_exists($sitemapPath)) {
            return Response::file($sitemapPath, [
                'Content-Type' => 'application/xml'
            ]);
        }
        
        // If sitemap file doesn't exist, generate dynamically
        return $this->generateSitemap();
    }
    
    private function generateSitemap()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';
        
        // Main pages
        $pages = [
            ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('/#tools'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/#how-it-works'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/#faq'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            // Tool pages
            ['loc' => url('/#dft-pro-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#android-multi-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#cf-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#cheetah-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#hydra-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#griffin-unlocker'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#tfm-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#tsm-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#anonyshu-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#mdm-fix-tool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/#unlocktool'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];
        
        foreach ($pages as $page) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $page['loc'] . '</loc>';
            $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $sitemap .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
            $sitemap .= '<priority>' . $page['priority'] . '</priority>';
            $sitemap .= '<xhtml:link rel="alternate" hreflang="pt" href="' . $page['loc'] . '"/>';
            $sitemap .= '<xhtml:link rel="alternate" hreflang="pt-BR" href="' . $page['loc'] . '"/>';
            $sitemap .= '</url>';
        }
        
        $sitemap .= '</urlset>';
        
        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}