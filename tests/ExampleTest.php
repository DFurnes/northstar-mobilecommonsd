<?php

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $content = json_decode($this->response->getContent());

        $this->assertEquals($content->lumen, $this->app->version());
    }
}
