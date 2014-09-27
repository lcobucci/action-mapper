<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

/**
 * Basic error handler
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class DefaultHandler extends ErrorHandler
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
        parent::__construct();

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
     * Renders the error page according with the exception
     *
     * @param HttpException $error
     * @return string
     */
    protected function getErrorContent(HttpException $error)
    {
        $acceptableContent = $this->request->getAcceptableContentTypes();

        $data = array(
            'code' => $error->getStatusCode(),
            'message' => $error->getMessage()
        );

        if ($this->displayTrace) {
            $data['trace'] = $error->__toString();
        }

        if (in_array('text/html', $acceptableContent)) {
            return $this->getHtmlContent($data);
        }

        if (in_array('application/xml', $acceptableContent)
            || in_array('application/x-xml', $acceptableContent)
            || in_array('text/xml', $acceptableContent)) {
            return $this->getXmlContent($data);
        }

        return $this->getJsonContent($data);
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
    protected function getXmlContent(array $data)
    {
        $this->response->setContentType('application/xml', 'UTF-8');

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
    protected function getJsonContent(array $data)
    {
        $this->response->setContentType('application/json', 'UTF-8');

        return json_encode($data);
    }
}
