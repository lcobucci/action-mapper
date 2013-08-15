<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Request;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class DefaultHandler extends ErrorHandler
{
    /**
     * @var string
     */
    private $content;

    /**
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
        $this->displayTrace = $displayTrace;
    }

    /**
     * @see ErrorHandler::getErrorContent()
     */
    protected function getErrorContent(
        Request $request,
        Response $response,
        HttpException $error
    ) {
        $acceptableContent = $request->getAcceptableContentTypes();

        $data = array(
            'code' => $error->getStatusCode(),
            'message' => $error->getMessage()
        );

        if ($this->displayTrace) {
            $data['trace'] = $error->__toString();
        }

        if (in_array('text/html', $acceptableContent)) {
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

        if (in_array('application/xml', $acceptableContent)
            || in_array('application/x-xml', $acceptableContent)
            || in_array('text/xml', $acceptableContent)) {
            $response->setContentType('application/xml', 'UTF-8');

            return '<?xml version="1.0" encoding="UTF-8"?>
                    <error>
                        <code>' . $data['code'] . '</code>
                        <message><![CDATA[' . $data['message'] . ']]></message>'
                        . ($this->displayTrace ? '<trace><![CDATA[' . $data['trace'] . ']]></trace>' : '')
                    . '</error>';
        }

        $response->setContentType('application/json', 'UTF-8');

        return json_encode($data);
    }
}
