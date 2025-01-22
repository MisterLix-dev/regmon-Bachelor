<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
class WikipediaBeispiel extends TestCase
{
    private $driver;

    protected function setUp(): void
    {
        $this->driver = RemoteWebDriver::create(
            'http://selenium:4444/wd/hub', // Selenium Server URL
            ['browserName' => 'chrome',
                'goog:chromeOptions' => [
                    'args' => ['--disable-dev-shm-usage']
                ]
            ],
            10000,
            5000
        );
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }

    public function testOpenAndVerifyText(): void
    {
        // Öffne eine andere Webseite
        $url = 'https://www.wikipedia.org';
        $this->driver->get($url);

        // Warte, bis der Haupttitel sichtbar ist
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 10);
        $mainTitle = $wait->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//h1'))
        );

        // Überprüfe den Text im Haupttitel
        $this->assertStringContainsString('Wikipedia', $mainTitle->getText());
        // Erwarteter Titel
        $expectedTitle = 'Wikipedia The Free Encyclopedia';
        $actualTitle = $mainTitle->getText();

        if ($actualTitle === $expectedTitle) {
            echo "Test erfolgreich: Die Webseite enthält den Titel '$expectedTitle'.\n";
        } else {
            echo "Test fehlgeschlagen: Erwarteter Titel war '$expectedTitle', tatsächlicher Titel ist '$actualTitle'.\n";
        }
    }
}
