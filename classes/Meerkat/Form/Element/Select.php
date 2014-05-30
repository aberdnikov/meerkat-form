<?php

namespace Meerkat\Form;

class Element_Select extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Select
     */
    static function factory($element) {
        return new Element_Select($element);
    }

    /**
     * 
     * @param type $options
     * @return \Meerkat\Form\Element_Select
     */
    function load_options($options) {
        $this->element->loadOptions($options);
        return $this;
    }

    function add_option($text, $value, $attributes = null) {
        $this->element->addOption($text, $value, $attributes);
        return $this;
    }

    function add_optgroup($label, $attributes = null) {
        $this->element->addOptgroup($label, $attributes);
        return $this;
    }

    /**
     * @param type $is_multiple
     * @return \Meerkat\Form\Element_Select
     */
    function set_multiple($is_multiple) {
        if ((bool) $is_multiple) {
            $this->element->setAttribute('multiple', 'multiple');
        } else {
            $this->element->removeAttribute('multiple', 'multiple');
        }
        return $this;
    }

    /**
     * 
     * @param type $size
     * @return \Meerkat\Form\Element_Select
     */
    function set_size($size) {
        $this->element->setAttribute('size', $size);
        return $this;
    }

}
