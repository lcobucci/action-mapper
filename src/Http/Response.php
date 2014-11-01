<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Http;

/**
 * Abstraction for HTTP response
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * Append a content to the current response body
     *
     * @param string $content
     * @return Response
     */
    public function appendContent($content)
    {
        return $this->setContent(
            $this->getContent() . $content
        );
    }

    /**
     * Configures the content type (and charset)
     *
     * @param string $contentType
     * @param string $charset
     * @return Response
     */
    public function setContentType($contentType, $charset = null)
    {
        if ($charset === null) {
            $charset = $this->charset ?: 'UTF-8';
        }

        $this->headers->set(
            'Content-Type',
            $contentType . '; charset=' . $charset
        );

        return $this;
    }

    /**
     * Redirect to given URI
     *
     * @param string $url
     * @param int $statusCode
     */
    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        $this->headers->set('Location', $url);
    }

    /**
     * Send the response to client
     *
     * @see \Symfony\Component\HttpFoundation\Response::send()
     */
    public function send()
    {
        parent::send();

        $this->terminateRequest();
    }

    /**
     * Finishes the response
     */
    protected function terminateRequest()
    {
        exit();
    }
}
