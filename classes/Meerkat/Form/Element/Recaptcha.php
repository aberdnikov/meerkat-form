<?php

namespace Meerkat\Form;

class Element_Recaptcha extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Recaptcha
     */
    static function factory($element) {
        return new Element_Recaptcha($element);
    }

}
