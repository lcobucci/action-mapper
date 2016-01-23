<?php
namespace Lcobucci\ActionMapper2\Http;

/**
 * Response test case.
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @param $contentType
     * @param $charset
     * @param $expected
     * @dataProvider contentTypeDataProvider
     */
    public function testIfContentType($contentType, $charset, $expected)
    {
        $response = new Response();
        $response->setContentType($contentType, $charset);

        $this->assertEquals($expected, $response->headers->get('Content-Type'));
    }

    /**
     * @dataProvider
     */
    public function contentTypeDataProvider()
    {
        return array(
            array('application/json', null, 'application/json; charset=UTF-8'),
            array('application/json', 'UTF-8', 'application/json; charset=UTF-8'),
            array('application/json', 'ISO-8859-1', 'application/json; charset=ISO-8859-1'),
            array('text/html', 'ISO-8859-1', 'text/html; charset=ISO-8859-1'),
            array('text/html', null, 'text/html; charset=UTF-8')
        );
    }

    /**
     * @test
     */
    public function testIfContentIsAppendedCorrectly()
    {
        $response = new Response();
        $response->setContent('<html>');
        $this->assertEquals('<html>', $response->getContent());

        $response->appendContent('</html>');
        $this->assertEquals('<html></html>', $response->getContent());
    }

    /**
     * @test
     * @param $url
     * @param $statusCode
     * @dataProvider redirectDataProvider
     */
    public function testIfRedirectReturnsCorrectHttpCodeAndLocationHeader($url, $statusCode)
    {
        $response = new Response();
        $response->redirect($url, $statusCode);

        $this->assertEquals($url, $response->headers->get('Location'));
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * @dataProvider
     */
    public function redirectDataProvider()
    {
        return array(
            array('http://www.google.com.br', 200, 'http://www.google.com.br'),
            array('http://www.google.com', 302, 'http://www.google.com'),
        );
    }
}
