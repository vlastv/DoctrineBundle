<?php

namespace Doctrine\Bundle\DoctrineBundle\Tests;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\Bundle\DoctrineBundle\LazyLoadingEventManager;

class LazyLoadingEventManagerTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->container = new Container();
		$this->evm = new LazyLoadingEventManager($this->container);
	}
	
	public function testDispatchEvent()
	{
		$this->container->set('foobar', $listener1 = new MyListener());
		$this->evm->addEventListener('foo', 'foobar');
		$this->evm->addEventListener('foo', $listener2 = new MyListener());
		
		$this->evm->dispatchEvent('foo');
		
		$this->assertTrue($listener1->called);
		$this->assertTrue($listener2->called);
	}
	
	public function testRemoveEventListener()
	{
		$this->evm->addEventListener('foo', 'bar');
		$this->evm->addEventListener('foo', $listener = new MyListener());
		
		$listeners = array('foo' => array('_service_bar' => 'bar', spl_object_hash($listener) => $listener));
		$this->assertSame($listeners, $this->evm->getListeners());
		$this->assertSame($listeners['foo'], $this->evm->getListeners('foo'));
		
		$this->evm->removeEventListener('foo', $listener);
		$this->assertSame(array('_service_bar' => 'bar'), $this->evm->getListeners('foo'));

		$this->evm->removeEventListener('foo', 'bar');
		$this->assertSame(array(), $this->evm->getListeners('foo'));
	}
}

class MyListener
{
	public $called = false;
	
	public function foo() 
	{
		$this->called = true;
	}
}