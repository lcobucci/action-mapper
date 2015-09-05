<?php
namespace Lcobucci\ActionMapper2\Http;

/**
 * Response test case.
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
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
}
