<?php

namespace Meerkat\Form;

class Element_Radio extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Radio
     */
    static function factory($element) {
        return new Element_Radio($element);
    }

    function is_inline($value=1) {
        $this->element->is_inline = (bool)$value;
        return $this;
    }

}
