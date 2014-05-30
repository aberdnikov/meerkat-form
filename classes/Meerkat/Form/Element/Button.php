<?php

namespace Meerkat\Form;

class Element_Button extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Button
     */
    static function factory($element) {
        return new Element_Button($element);
    }

    function set_content($value) {
        $this->element->setOption('content', $value);
        return $this;
    }

}
