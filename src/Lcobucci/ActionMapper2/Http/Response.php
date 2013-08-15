<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Http;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
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
     * @param string $url
     */
    public function redirect($url)
    {
        $this->headers->set('Location', $url);
    }

    /**
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
