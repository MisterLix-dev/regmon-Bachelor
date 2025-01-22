<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class LoginTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://localhost']);
    }

    public function testSuccessfulLogin()
    {
        // Test erfolgreiches Login
        $response = $this->client->post('/login', [
            'form_params' => [
                'username' => 'testuser',  // Testbenutzername
                'password' => 'testpassword',  // Testpasswort
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Willkommen, testuser', $response->getBody());
    }

    public function testFailedLoginWrongPassword()
    {
        // Test mit falschem Passwort
        $response = $this->client->post('/login', [
            'form_params' => [
                'username' => 'testuser',
                'password' => 'wrongpassword',
            ],
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('Ungültige Anmeldedaten', $response->getBody());
    }

    public function testFailedLoginCaptcha() {
        $_POST['username'] = 'valid_username';  // Existierender Benutzername im System
        $_POST['password'] = 'correct_password';  // Richtiges Passwort
        $_POST['form_submit'] = '1';
        $_POST['visualCaptcha'] = 'incorrect_captcha';  // Falsche Captcha-Antwort

        $response = file_get_contents('http://localhost/authenticate.php', false, stream_context_create(['http' => ['method' => 'POST']]));
        $location_header = '';

        // Überprüft, ob die URL korrekt auf die Fehlerseite weiterleitet
        $this->assertStringContainsString('login.php?captchaError=1', $response);
    }

}
