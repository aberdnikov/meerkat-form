<?php

namespace Meerkat\Form;

class Element_Text extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Text
     */
    static function factory($element) {
        return new Element_Text($element);
    }

    function set_placeholder($placeholder) {
        $this->element->setAttribute('placeholder', $placeholder);
        return $this;
    }

}
