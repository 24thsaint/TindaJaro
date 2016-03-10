<?php

namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase {

    /**
    * As an administrator,
    * I want my users to have create an account before using the site
    * So that their account shall be used on all transactions
    */

    /**
    * Given that I am an anonymous user
    * When I visit the homepage
    * Then I will see a login form
    */
    public function testForm() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertCount(2, $crawler->filter('input'));
        $this->assertContains(
            'Username',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Password',
            $client->getResponse()->getContent()
        );
    }

    /**
    * Given that I am an anonymous user
    * When I visit the homepage
    * I will be redirected to the login form
    */
    public function testHomepageAnonymousLoginRedirection() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue(
            $client->getResponse()->isRedirect('/login')
        );
    }

    /**
    * Given that I am on the registration page
    * When I fill the forms up with valid values
    * Then the system will accept my form and indicate that my registration is successful
    */
    //SKIP REGISTRATION
    public function testValidRegistration() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register!')->form();
        $form['user[firstname]'] = 'Rave';
        $form['user[lastname]'] = 'Arevalo';
        $form['user[mobilenumber]'] = '09478969902';
        $form['user[homeaddress]'] = '161 San Jose Street, Jaro, Iloilo City';
        $form['user[username]'] = '24thsaint';
        $form['user[email]'] = 'nironjin@gmail.com';
        $form['user[plainPassword][first]'] = '123123';
        $form['user[plainPassword][second]'] = '123123';
        $crawler = $client->submit($form);
        $client->followRedirect();
        $this->assertContains(
            'Registration Successful!',
            $client->getResponse()->getContent()
        );
    }

    /**
    * Given that I am on the login page
    * When I enter valid credentials
    * Then I will be authenticated and redirected to the Overview page
    */
    public function testValidLoginCredentials() {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = '24thsaint';
        $form['_password'] = '123123';
        $crawler = $client->submit($form);

        $this->assertContains(
            'Welcome',
            $client->getResponse()->getContent()
        );
    }

    /**
    * Given that I am on the login page
    * When I enter invalid credentials
    * Then I will stay on the page with an 'Bad credentials' warning
    */
    public function testInvalidLoginCredentials() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'fake';
        $form['_password'] = 'fake';
        $crawler = $client->submit($form);
        $client->followRedirect();
        $this->assertContains(
            'invalid-credentials',
            $client->getResponse()->getContent()
        );
    }

}


?>
