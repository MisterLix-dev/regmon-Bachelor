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
    public function testHTMLCode(): void {
        $this->driver->get('localhost:8000');
        $actualURL = $this->driver->getCurrentURL();
        echo "Current URL: $actualURL\n";
        $pageSource = $this->driver->getPageSource();

        // Output the page source to the console
        echo "Page Source:\n";
        echo $pageSource;
        if (empty($pageSource)) {
            echo "Page source is empty. Check if the page is accessible.\n";
        } else {
            echo "Page source retrieved successfully.\n";
        }
        // Optionally save it to a file for inspection
        #file_put_contents('page_source.html', $pageSource);

        // Add an assertion to ensure the page loads successfully
        $this->assertNotEmpty($pageSource, "The page source is empty. The page may not have loaded correctly.");


    }

}
