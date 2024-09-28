<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class WebScraperService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function scrapeUdemy()
    {
        $url = 'https://www.udemy.com/courses/search/?q=programming';
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to retrieve the page from Udemy');
        }

        $content = $response->getContent();
        $crawler = new Crawler($content);

        $titles = $crawler->filter('div.course-card--course-title--2f7tE')->each(function (Crawler $node) {
            return $node->text();
        });

        return $titles;
    }

    public function scrapeCoursera()
    {
        $url = 'https://www.coursera.org/courses?query=programming';
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to retrieve the page from Coursera');
        }

        $content = $response->getContent();
        $crawler = new Crawler($content);

        $titles = $crawler->filter('h2.color-primary-text.card-title.headline-1-text')->each(function (Crawler $node) {
            return $node->text();
        });

        return $titles;
    }

    public function scrapeEdx()
    {
        $url = 'https://www.edx.org/search?q=programming';
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to retrieve the page from edX');
        }

        $content = $response->getContent();
        $crawler = new Crawler($content);

        $titles = $crawler->filter('h3.card-title')->each(function (Crawler $node) {
            return $node->text();
        });

        return $titles;
    }
}
