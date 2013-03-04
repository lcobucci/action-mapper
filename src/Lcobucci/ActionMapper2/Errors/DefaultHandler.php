<?php
namespace Lcobucci\ActionMapper2\Errors;

use \Lcobucci\ActionMapper2\Http\Response;
use \Lcobucci\ActionMapper2\Http\Request;

class DefaultHandler extends ErrorHandler
{
    /**
     * @var string
     */
    private $content;

    /**
     * Class constructor
     *
     * @param string $templateFile
     */
    public function __construct($templateFile = null)
    {
        parent::__construct();

        if ($templateFile === null || !file_exists($templateFile)) {
            $templateFile = __DIR__ . '/ErrorPage.phtml';
        }

        $this->content = file_get_contents($templateFile);
    }

    /**
     *
     * @see \Lcobucci\ActionMapper2\Errors\ErrorHandler::getErrorContent()
     */
    protected function getErrorContent(
        Request $request,
        Response $response,
        HttpException $error
    ) {
        $acceptableContent = $request->getAcceptableContentTypes();

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
                    $error->getStatusCode(),
                    $error->getMessage(),
                    $error
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
                        <code>' . $error->getStatusCode() . '</code>
                        <message><![CDATA[' . $error->getMessage() . ']]></message>
                        <trace><![CDATA[' . $error . ']]></trace>
                    </error>';
        }

        $response->setContentType('application/json', 'UTF-8');

        return json_encode(
            array(
                'code' => $error->getStatusCode(),
                'message' => $error->getMessage(),
                'trace' => $error->__toString()
            )
        );
    }
}
