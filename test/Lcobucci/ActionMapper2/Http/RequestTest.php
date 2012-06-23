<?php
namespace Lcobucci\ActionMapper2\Http;

/**
 * Request test case.
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function requestedPathMustBeEqualsToPathInfoWhenNotChanged()
    {
        $request = $this->getMock(
            '\Lcobucci\ActionMapper2\Http\Request',
            array('getPathInfo'),
            array(),
            '',
            false
        );

        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue('/test'));

        $this->assertEquals('/test', $request->getRequestedPath());
    }

    /**
     * @test
     */
    public function requestedPathCanBeChanged()
    {
        $request = $this->getMock(
                '\Lcobucci\ActionMapper2\Http\Request',
                array('getPathInfo'),
                array(),
                '',
                false
        );

        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue('/test'));

        $request->setRequestedPath('/news/2012');

        $this->assertEquals('/news/2012', $request->getRequestedPath());
    }

    /**
     * @test
     */
    public function trailingBarMustBeRemoved()
    {
        $request = $this->getMock(
                '\Lcobucci\ActionMapper2\Http\Request',
                array('getPathInfo'),
                array(),
                '',
                false
        );

        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue('/test/'));

        $this->assertEquals('/test', $request->getRequestedPath());
    }

    /**
     * @test
     */
    public function trailingBarCannotBeRemovedIfPathIsBar()
    {
        $request = $this->getMock(
                '\Lcobucci\ActionMapper2\Http\Request',
                array('getPathInfo'),
                array(),
                '',
                false
        );

        $request->expects($this->any())
                ->method('getPathInfo')
                ->will($this->returnValue('/'));

        $this->assertEquals('/', $request->getRequestedPath());
    }
}