<?php

namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VendorControllerTest extends WebTestCase {

    /**
    * Given that I am a logged in user and I want to sell a product
    * When I enter valid values on the form
    * Then I the system should accept my entry and I should see and acknowledgment message
    */
    public function testvalidProductEntry() {
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

        $crawler = $client->request('GET', '/vendor/mystore');
        $this->assertContains(
            'Add New Product',
            $client->getResponse()->getContent()
        );

        $link = $crawler->selectLink('Add New Product')->link();

        $crawler = $client->click($link);

        $this->assertCount(
            5,
            $crawler->filter('input')
        );

        $name = "Dried Fish";

        $form = $crawler->selectButton('Add Product')->form();
        $form['product[name]'] = $name;
        $form['product[quantity]'] = 100;
        $form['product[price]'] = 400;
        $form['product[description]'] = "Fresh dried fish from Palawan";
        $form['product[image]'] = "http://images.fish.com/fish.jpg";
        $crawler = $client->submit($form);

        $this->assertContains('Your product '.$name.' was successfully added to your store!', $client->getResponse()->getContent());

        // I can see my product added to the table
        $this->assertContains('Dried Fish', $client->getResponse()->getContent());
    }

}

?>
