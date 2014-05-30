<?php

namespace Meerkat\Form;

class Element_Textarea extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Textarea
     */
    static function factory($element) {
        return new Element_Textarea($element);
    }

    /**
     * @param type $value
     * @return \Meerkat\Form\Element_Textarea
     */
    function set_cols($value) {
        $this->element->setAttribute('cols', $value);
        return $this;
    }

    /**
     * @param type $value
     * @return \Meerkat\Form\Element_Textarea
     */
    function set_rows($value) {
        $this->element->setAttribute('rows', $value);
        return $this;
    }

}
