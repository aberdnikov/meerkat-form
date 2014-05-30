<?php

namespace Meerkat\Form;

class Element_Checkbox extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Node
     */
    static function factory($element) {
        return new Element_Checkbox($element);
    }

    function is_inline($value = 1) {
        $this->element->is_inline = (bool) $value;
        return $this;
    }

}
