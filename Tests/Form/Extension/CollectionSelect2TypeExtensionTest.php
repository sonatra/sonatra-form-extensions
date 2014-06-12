<?php

/**
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Form\Extension;

use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\BaseChoiceSelect2TypeExtension;
use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\ChoiceSelect2TypeExtension;
use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\CollectionSelect2TypeExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;

/**
 * Tests case for collection of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class CollectionSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function setUp()
    {
        parent::setUp();

        /* @var ContainerInterface $container */
        $container = $this->container;

        $includeFactory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new ChoiceSelect2TypeExtension($container, 'currency', 10))
            ->addTypeExtension(new BaseChoiceSelect2TypeExtension('currency'))
            ->getFormFactory();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new ChoiceSelect2TypeExtension($container, 'currency', 10))
            ->addTypeExtension(new BaseChoiceSelect2TypeExtension('currency'))
            ->addTypeExtension(new CollectionSelect2TypeExtension($includeFactory, $container, $this->getExtensionTypeName(), 10))
            ->getFormFactory();

        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function getExtensionTypeName()
    {
        return 'collection';
    }

    protected function mergeOptions(array $options)
    {
        $options = parent::mergeOptions($options);
        $options['type'] = 'currency';
        $options['select2'] = array_merge_recursive(isset($options['select2']) ? $options['select2'] : array(), array('enabled' => true));

        return $options;
    }

    protected function getSingleData()
    {
        return array('EUR');
    }

    protected function getValidSingleValue()
    {
        return 'EUR';
    }

    protected function getValidAjaxSingleValue()
    {
        return 'EUR';
    }

    protected function getMultipleData()
    {
        return array('EUR', 'USD');
    }

    protected function getValidMultipleValue()
    {
        return array('EUR', 'USD');
    }

    protected function getValidAjaxMultipleValue()
    {
        return implode(',', $this->getValidMultipleValue());
    }

    protected function getValidFirstChoiceSelected()
    {
        return array();
    }

    public function testDefaultOptions()
    {
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions(array()));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->getOption('allow_add'));
        $this->assertTrue($config->getOption('allow_delete'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertTrue($config->getOption('select2')['enabled']);
        $this->assertFalse($config->getOption('select2')['ajax']);
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $config->getAttribute('choice_list'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    public function testDisabled()
    {
        $options = $this->mergeOptions(array());
        $options['select2']['enabled'] = false;
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $options);
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertFalse($config->getOption('select2')['enabled']);

        $view = $form->createView();
        $this->assertFalse(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $view->vars['value']);
    }

    public function testSingleWithTags()
    {
        $options = array('select2' => array('tags' => array()));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertTrue($config->getOption('select2')['enabled']);
        $this->assertFalse($config->getOption('select2')['ajax']);
        $this->assertTrue($config->getOption('allow_add'));
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $config->getAttribute('choice_list'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
        $this->assertTrue(array_key_exists('tags', $view->vars['select2']));
    }

    public function testSingleAjax()
    {
        $options = array('select2' => array('ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertTrue($config->getOption('select2')['enabled']);
        $this->assertTrue($config->getOption('select2')['ajax']);
        $this->assertTrue($config->getOption('allow_add'));
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $config->getAttribute('choice_list'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxSingleValue(), $view->vars['value']);
    }

    public function testSingleAjaxWithTags()
    {
        $options = array('select2' => array('ajax' => true, 'tags' => array()));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertTrue($config->getOption('select2')['enabled']);
        $this->assertTrue($config->getOption('select2')['ajax']);
        $this->assertTrue($config->getOption('allow_add'));
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $config->getAttribute('choice_list'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxSingleValue(), $view->vars['value']);
    }

    public function testMultiple()
    {
        $options = array('multiple' => true);
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getMultipleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertTrue($config->getOption('select2')['enabled']);
        $this->assertFalse($config->getOption('select2')['ajax']);
        $this->assertTrue($config->getOption('allow_add'));
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $config->getAttribute('choice_list'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getMultipleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxMultipleValue(), $view->vars['value']);
    }

    public function testMultipleAjax()
    {
        $options = array('multiple' => true, 'select2' => array('ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getMultipleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $this->assertTrue($config->getOption('select2')['enabled']);
        $this->assertTrue($config->getOption('select2')['ajax']);
        $this->assertTrue($config->getOption('allow_add'));
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $config->getAttribute('choice_list'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getMultipleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxMultipleValue(), $view->vars['value']);
    }

    public function testRequiredAjaxFirstChoice()
    {
        $options = array('select2' => array('ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertEquals($this->getValidFirstChoiceSelected(), $view->vars['choices_selected']);
    }

    public function testSinglePlaceHolder()
    {
        // Skip test
    }

    public function testAjaxRoute()
    {
        $options = array('required' => false, 'select2' => array('ajax' => true, 'ajax_route' => 'foobar'));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertEquals('/foobar', $view->vars['select2']['ajax_url']);
    }

    public function testWithoutChoiceList()
    {
        $options = $this->mergeOptions(array());
        $options['type'] = 'text';

        $form = $this->factory->create($this->getExtensionTypeName(), null, $options);
        $view = $form->createView();

        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\AjaxChoiceListInterface', $form->getConfig()->getAttribute('choice_list'));
        $this->assertArrayHasKey('select2', $view->vars);
    }
}