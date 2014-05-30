<?php

namespace Meerkat\Form;

class Element_Submit extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Submit
     */
    static function factory($element) {
        return new Element_Submit($element);
    }

    function set_label($value) {
        $this->element->setAttribute('value', $value);
        return $this;
    }

}
