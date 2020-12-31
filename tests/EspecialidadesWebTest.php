<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadesWebTest extends WebTestCase
{
    public function testeRespostaNaoAutenticado()
    {
        $client = static::createClient();
        $client->request('GET', '/especialidades');

        self::assertEquals(
            401,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testeListarEspecialidades()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request('GET', '/especialidades', [], [], [
            'HTTP_TOKEN' => "Bearer $token"
        ]);

        $resposta = json_decode($client->getResponse()->getContent());
        self::assertTrue($resposta->success);
    }

    public function testeInsereEspecialidade()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request(
            'POST',
            '/especialidades',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_TOKEN' => "Bearer $token"
            ],
            json_encode([
                'descricao' => 'Teste'
            ])
        );
        
        self::assertEquals(
            201, 
            $client->getResponse()->getStatusCode()
        );
    }

    protected function login(KernelBrowser $client): string
    {
        $client->request(
            'POST', 
            '/login', 
            [], 
            [], 
            [
                'CONTENT_TYPE' => "application/json"
            ],
            json_encode([
                'usuario' => 'user',
                'senha' => 123456
            ])
        );

        return json_decode($client->getResponse()->getContent())->content->access_token;
    }
}