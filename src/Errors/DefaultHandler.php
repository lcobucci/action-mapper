<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

use Lcobucci\ActionMapper\Http\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The default error handler
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class DefaultHandler extends BaseHandler
{
    /**
     * The template content
     *
     * @var string
     */
    private $content;

    /**
     * Show the exception trace
     *
     * @var boolean
     */
    private $displayTrace;

    /**
     * Class constructor
     *
     * @param string $templateFile
     * @param boolean $displayTrace
     */
    public function __construct($templateFile = null, $displayTrace = true)
    {
        if ($templateFile === null || !file_exists($templateFile)) {
            $templateFile = __DIR__ . '/ErrorPage.phtml';
        }

        $this->content = file_get_contents($templateFile);
        $this->setDisplayTrace($displayTrace);
    }

    /**
     * Configures if trace should be displayed
     *
     * @param boolean $displayTrace
     */
    public function setDisplayTrace($displayTrace)
    {
        $this->displayTrace = (bool) $displayTrace;
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorContent(Request $request, Response $response, Exception $exception)
    {
        $acceptableContent = $request->getAcceptableContentTypes();

        $data = array(
            'code' => $exception->getStatusCode(),
            'message' => $exception->getMessage()
        );

        if ($this->displayTrace) {
            $data['trace'] = $exception->__toString();
        }

        if (in_array('text/html', $acceptableContent)) {
            return $this->getHtmlContent($data);
        }

        if (in_array('application/xml', $acceptableContent)
            || in_array('application/x-xml', $acceptableContent)
            || in_array('text/xml', $acceptableContent)) {
            return $this->getXmlContent($response, $data);
        }

        return $this->getJsonContent($response, $data);
    }

    /**
     * Returns the error message as a HTML page
     *
     * @param array $data
     * @return ErrorPage
     */
    protected function getHtmlContent(array $data)
    {
        return str_replace(
            array(
                '{title}',
                '{statusCode}',
                '{message}',
                '{trace}'
            ),
            array(
                'An error has occurred...',
                $data['code'],
                $data['message'],
                $this->displayTrace ? $data['trace'] : ''
            ),
            $this->content
        );
    }

    /**
     * Returns the error message as a XML object
     *
     * @param array $data
     * @return string
     */
    protected function getXmlContent(Response $response, array $data)
    {
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return '<?xml version="1.0" encoding="UTF-8"?>
                <error>
                    <code>' . $data['code'] . '</code>
                    <message><![CDATA[' . $data['message'] . ']]></message>'
                    . ($this->displayTrace ? '<trace><![CDATA[' . $data['trace'] . ']]></trace>' : '')
                . '</error>';
    }

    /**
     * Returns the error message as a JSON object
     *
     * @param array $data
     * @return string
     */
    protected function getJsonContent(Response $response, array $data)
    {
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return json_encode($data);
    }
}
