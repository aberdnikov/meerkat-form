<?php

namespace Meerkat\Form;

use \HTML_QuickForm2_Node as HTML_QuickForm2_Node;
use \Debug as Debug;
use \Arr as Arr;

/**
 * Abstract base class for QuickForm2 renderers
 */
require_once 'HTML/QuickForm2/Renderer/Array.php';

/**
 * Default renderer for QuickForm2
 *
 * Mostly a direct port of Default renderer from QuickForm 3.x package.
 *
 * While almost everything in this class is defined as public, its properties
 * and those methods that are not published (i.e. not in array returned by
 * exportMethods()) will be available to renderer plugins only.
 *
 * The following methods are published:
 *   - {@link reset()}
 *   - {@link setTemplateForClass()}
 *   - {@link setTemplateForId()}
 *   - {@link setErrorTemplate()}
 *   - {@link setElementTemplateForGroupClass()}
 *   - {@link setElementTemplateForGroupId()}
 *
 * @category   HTML
 * @package    HTML_QuickForm2
 * @author     Alexey Borzov <avb@php.net>
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @version    Release: 0.6.0
 */
class Renderer extends \HTML_QuickForm2_Renderer_Array {

    /**
     * An array being generated
     * @var array
     */
    public $array = array();

    /**
     * Array with references to 'elements' fields of currently processed containers
     * @var array
     */
    public $containers = array();

    /**
     * Whether the form contains required elements
     * @var  bool
     */
    public $hasRequired = false;

    /**
     * Additional style information for elements
     * @var array
     */
    public $styles = array();

    /**
     * Constructor, adds a new 'static_labels' option
     */
    protected function __construct() {
        $this->options['static_labels'] = false;
    }

    protected function exportMethods() {
        return array(
            'toArray',
            'setStyleForId'
        );
    }

    /**
     * Resets the accumulated data
     *
     * This method is called automatically by startForm() method, but should
     * be called manually before calling other rendering methods separately.
     *
     * @return HTML_QuickForm2_Renderer_Array
     */
    public function reset() {
        $this->array = array();
        $this->containers = array();
        $this->hasRequired = false;

        return $this;
    }

    /**
     * Returns the resultant array
     *
     * @return array
     */
    public function toArray() {
        ///\Debug::stop($this->array);
        return $this->array;
    }

    /**
     * Creates an array with fields that are common to all elements
     *
     * @param HTML_QuickForm2_Node $element Element being rendered
     *
     * @return   array
     */
    public function buildCommonFields(HTML_QuickForm2_Node $element) {
        //Debug::stop($element);
        $ary = array(
            'id' => $element->getId(),
            'frozen' => $element->toggleFrozen()
        );
        //Debug::info($element->getLabel(),__METHOD__);
        if ($labels = $element->getLabel()) {
            $ary['label'] = Arr::get($labels, 0);
            $ary['desc'] = Arr::get($labels, 1);
            $ary['example'] = Arr::get($labels, 2);
        }
        $ary['is_inline'] = (isset($element->is_inline) && $element->is_inline);
        $ary['prepend'] = (isset($element->prepend) && $element->prepend) ? $element->prepend : '';
        $ary['append'] = (isset($element->append) && $element->append) ? $element->append : '';
        if (($error = $element->getError()) && $this->options['group_errors']) {
            $this->array['errors'][$ary['id']] = $error;
        } elseif ($error) {
            $ary['error'] = $error;
        }
        return $ary;
    }

    /**
     * Creates an array with fields that are common to all Containers
     *
     * @param HTML_QuickForm2_Node $container Container being rendered
     *
     * @return array
     */
    public function buildCommonContainerFields(HTML_QuickForm2_Node $container) {
        return $this->buildCommonFields($container) + array(
            'elements' => array(),
            'attributes' => $container->getAttributes(true)
        );
    }

    /**
     * Stores an array representing "scalar" element in the form array
     *
     * @param array $element
     */
    public function pushScalar(array $element) {
        if (!empty($element['required'])) {
            $this->hasRequired = true;
        }
        if (empty($this->containers)) {
            $this->array += $element;
        } else {
            $this->containers[count($this->containers) - 1][] = $element;
        }
    }

    /**
     * Stores an array representing a Container in the form array
     *
     * @param array $container
     */
    public function pushContainer(array $container) {
        if (!empty($container['required'])) {
            $this->hasRequired = true;
        }
        if (empty($this->containers)) {
            $this->array += $container;
            $this->containers = array(&$this->array['elements']);
        } else {
            $cntIndex = count($this->containers) - 1;
            $myIndex = count($this->containers[$cntIndex]);
            $this->containers[$cntIndex][$myIndex] = $container;
            $this->containers[$cntIndex + 1] = & $this->containers[$cntIndex][$myIndex]['elements'];
        }
    }

    /*     * #@+
     * Implementations of abstract methods from {@link HTML_QuickForm2_Renderer}
     */

    public function renderElement(HTML_QuickForm2_Node $element) {
        $ary = $this->buildCommonFields($element) + array(
            'html' => $element->__toString(),
            'value' => $element->getValue(),
            'type' => $element->getType(),
            'required' => $element->isRequired(),
            'col_wrap' => isset($element->col_wrap)?$element->col_wrap:'',
            'wrap_class' => isset($element->wrap_class)?$element->wrap_class:'',
            'element_size' => isset($element->element_size)?$element->element_size:'',
        );
        $this->pushScalar($ary);
    }

    public function renderHidden(HTML_QuickForm2_Node $element) {
        $this->array['hidden'][] = $element->__toString();
    }

    public function startForm(HTML_QuickForm2_Node $form) {
        $this->reset();
        $this->array = $this->buildCommonContainerFields($form);
        if ($this->options['group_errors']) {
            $this->array['errors'] = array();
        }
        $this->array['hidden'] = array();
        $this->containers = array(&$this->array['elements']);
    }

    public function finishForm(HTML_QuickForm2_Node $form) {
        $this->finishContainer($form);
        if ($this->hasRequired) {
            $this->array['required_note'] = '* Поля, обязательные для заполнения';
        }
        $script = $this->getJavascriptBuilder()->getFormJavascript($form->getId(), false);
        //Debug::stop($script);
        Form::set_js($form->getId(), $script);
        //\Meerkat\StaticFiles\Js::instance()->add_inline($script);
    }

    public function startContainer(HTML_QuickForm2_Node $container) {
        $ary = $this->buildCommonContainerFields($container) + array(
            'required' => $container->isRequired(),
            'type' => $container->getType()
        );
        $this->pushContainer($ary);
    }

    public function finishContainer(HTML_QuickForm2_Node $container) {
        array_pop($this->containers);
    }

    public function startGroup(HTML_QuickForm2_Node $group) {
        $ary = $this->buildCommonContainerFields($group) + array(
            'required' => $group->isRequired(),
            'type' => $group->getType(),
            'class' => $group->getAttribute('class'),
        );
        if ($separator = $group->getSeparator()) {
            $ary['separator'] = array();
            for ($i = 0, $count = count($group); $i < $count - 1; $i++) {
                if (!is_array($separator)) {
                    $ary['separator'][] = (string) $separator;
                } else {
                    $ary['separator'][] = $separator[$i % count($separator)];
                }
            }
        }
        $this->pushContainer($ary);
    }

    public function finishGroup(HTML_QuickForm2_Node $group) {
        $this->finishContainer($group);
    }

}