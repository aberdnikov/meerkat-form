<?php

namespace Meerkat\Form;

class Element_HierSelect extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_HierSelect
     */
    static function factory($element) {
        return new Element_HierSelect($element);
    }

    function load_options($options) {
        $this->element->loadOptions($options);
        return $this;
    }

    function set_separator($separator) {
        $this->element->setSeparator($separator);
        return $this;
    }

}
