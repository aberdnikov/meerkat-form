<?php

namespace Meerkat\Form;

class Element_Date extends Node {

    /**
     * @param type $element
     * @return \Meerkat\Form\Element_Date
     */
    static function factory($element) {
        return new Element_Date($element);
    }

    /**
     * @param type $format
     * @return \Meerkat\Form\Element_Date
     */
    function set_format($format) {
        $this->element->setOption('format', $format);
        return $this;
    }

    /**
     * @param type $year
     * @return \Meerkat\Form\Element_Date
     */
    function set_min_year($year) {
        $this->element->setOption('minYear', $year);
        return $this;
    }

    /**
     * @param type $year
     * @return \Meerkat\Form\Element_Date
     */
    function set_max_year($year) {
        $this->element->setOption('maxYear', $year);
        return $this;
    }

}
