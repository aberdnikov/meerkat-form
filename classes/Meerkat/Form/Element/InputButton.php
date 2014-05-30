<?php

namespace Meerkat\Form;

class Element_InputButton extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_InputButton
     */
    static function factory($element) {
        return new Element_InputButton($element);
    }

    function set_label($value) {
        $this->element->setAttribute('value', $value);
        return $this;
    }
}
