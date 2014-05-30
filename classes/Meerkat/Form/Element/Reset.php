<?php

namespace Meerkat\Form;

class Element_Reset extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Reset
     */
    static function factory($element) {
        return new Element_Reset($element);
    }

    function set_label($value) {
        $this->element->setAttribute('value', $value);
        return $this;
    }

}
