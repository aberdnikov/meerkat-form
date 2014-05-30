<?php

namespace Meerkat\Form;

class Element_Image extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Image
     */
    static function factory($element) {
        return new Element_Image($element);
    }

}
