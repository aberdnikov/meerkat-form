<?php

namespace Meerkat\Form;

class Node {

    /**
     * @v ar \HTML_QuickForm2_Node
     * @var \HTML_QuickForm2_Container
     */
    protected $element;

    /**
     * @param type $element
     * @return \Meerkat\Form\Node
     */
    static function factory($element) {
        return new Node($element);
    }

    function __construct($element) {
        if ($element instanceof Node) {
            $element = $element->get_element();
        }
        $this->element = $element;
    }

    function set_error($error) {
        $this->element->setError($error);
        return $this;
    }

    function set_prepend($value) {
        $this->element->prepend = $value;
        return $this;
    }

    function set_append($value) {
        $this->element->append = $value;
        return $this;
    }

    function set_value($value) {
        $this->element->setValue($value);
        return $this;
    }

    function set_id($value) {
        $this->element->setId($value);
        return $this;
    }

    function set_wrap_class($value) {
        $this->element->wrap_class = $value;
        return $this;
    }

    function set_element_size_large(){
        $this->element->addClass('input-lg')->removeClass('input-sm');
        $this->element->element_size = 'lg';
        return $this;
    }

    function set_element_size_small(){
        $this->element->addClass('input-sm')->removeClass('input-lg');
        $this->element->element_size = 'sm';
        return $this;
    }

    function set_element_size_default(){
        $this->element->removeClass('input-sm input-lg');
        $this->element->element_size = '';
        return $this;
    }

    function get_element() {
        return $this->element;
    }

    function get_form() {
        $parent = $this->element;
        while (!($parent instanceof \HTML_QuickForm2)) {
            $parent = $parent->getContainer();
        }
        return $parent;
    }

    function set_label($value) {
        $labels = (array) $this->element->getLabel();
        $labels[0] = $value;
        $this->element->setLabel($labels);
        return $this;
    }

    function set_desc($value) {
        $labels = (array) $this->element->getLabel();
        $labels[1] = $value;
        $this->element->setLabel($labels);
        return $this;
    }

    function set_example($value, $text = null) {
        $labels = (array) $this->element->getLabel();
        $text = $text ? $text : $value;
        $labels[2][] = array($value, $text);
        $this->element->setLabel($labels);
        \Meerkat\StaticFiles\Js::instance()->add_onload('
            jQuery(document).on("click",".frm-example span",function(){
                var id = jQuery(this).parent().attr("rel");
                var val = jQuery(this).attr("data-value");
                if(val.indexOf(",") + 1) {
                    jQuery("#"+id).val(val.split(","));
                } else {
                    jQuery("#"+id).val(val);
                }   
                jQuery("#"+id).trigger("blur");
            });
        ', 'frm-example');
        return $this;
    }

    function set_example_ext($value, $text = null) {
        $labels = (array) $this->element->getLabel();
        $text = $text ? $text : $value;
        $labels[2][] = array($value, $text);
        $this->element->setLabel($labels);
        \Meerkat\StaticFiles\Js::instance()->add_onload('
            jQuery(".frm-example").click(function(){
                var id = jQuery(this).parent().attr("rel");
                var val = jQuery(this).attr("data-value");
                var current = jQuery("#"+id).val();
                jQuery("#"+id).val(current ? current+","+val : val);
                jQuery("#"+id).trigger("change");
            });
        ', 'form-example2');
        return $this;
    }

    function set_attribute($name, $value) {
        $this->element->setAttribute($name, $value);
        return $this;
    }

    /**
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_required($error = 'Не заполнено') {
        $this->element->addRule('required', $error, null, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_non_empty($error = 'Не заполнено',$cnt=null) {
        $this->element->addRule('nonempty', $error, $cnt, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * 
     * @param type $callback
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_callback($callback, $error = 'Ошибка', $runAt = \HTML_QuickForm2_Rule::SERVER) {
        $this->element->addRule('callback', $error, $callback, $runAt);
        return $this;
    }

    /**
     * 
     * @param type $regexp '/^[a-zA-Z]+$/'
     * @param type $error  'Username should contain only letters' 
     * @return \Meerkat\Form\Node
     */
    function rule_regexp($regexp, $error = 'Не верный формат') {
        $this->element->addRule('regex', $error, $regexp, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * длиной не меньше
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_minlength($val, $error = null) {
        if (!$error) {
            $error = 'Минимум ' . $val . ' ' . \Meerkat\Helper\Helper_Text::plural($val, 'символ', 'символа', 'символов');
        }
        $this->element->addRule('minlength', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * длиной не больше
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_maxlength($val, $error = null) {
        if (!$error) {
            $error = 'Максимум ' . $val . ' ' . \Meerkat\Helper\Helper_Text::plural($val, 'символ', 'символа', 'символов');
        }
        $this->element->addRule('maxlength', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * больше либо равно указанному значению
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_gte($val, $error = null) {
        if (!$error) {
            $error = 'Больше либо равно ' . $val;
        }
        $this->element->addRule('gte', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * меньше либо равно указанному значению
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_lte($val, $error = null) {
        if (!$error) {
            $error = 'Меньше либо равно ' . $val;
        }
        $this->element->addRule('lte', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * строго меньше указанного значения
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_lt($val, $error = null) {
        if (!$error) {
            $error = 'Строго меньше ' . $val;
        }
        $this->element->addRule('lt', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * строго больше указанного значения
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_gt($val, $error = null) {
        if (!$error) {
            $error = 'Строго больше ' . $val;
        }
        $this->element->addRule('gt', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * не равно
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_neq($val, $error) {
        $this->element->addRule('neq', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    /**
     * равно
     * @param type $val
     * @param type $error
     * @return \Meerkat\Form\Node
     */
    function rule_eq($val, $error) {
        $this->element->addRule('eq', $error, $val, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
        return $this;
    }

    function add_class($class) {
        $this->element->addClass($class);
        return $this;
    }

    function rule_file_extensions(array $ext, $error = null) {
        if (!$error) {
            $err = 'Разрешенные расширения .:val';
        }
        $this->element->addRule('regex', __($error, array(':val' => implode(', .', $ext))), '/\\.(' . implode('|', $ext) . ')$/i');
        return $this;
    }

    function rule_file_mime(array $mime, $error = 'Не правильный mime-type загруженного файла') {
        $this->element->addRule('mimetype', __($error), $mime);
        return $this;
    }

    function rule_img_extensions($error = null) {
        $this->rule_file_extensions(array('gif', 'jpeg', 'jpg', 'png'), $error);
        return $this;
    }

    function rule_img_mime($error = 'Не правильный mime-type загруженного файла') {
        $this->rule_file_mime(array('image/gif', 'image/png', 'image/jpeg', 'image/pjpeg'), $error);
        return $this;
    }

    function filter($callback) {
        $this->element->addFilter($callback);
        return $this;
    }

    function filter_int() {
        $this->element->addFilter('intval');
        return $this;
    }

    function filter_trim() {
        $this->element->addFilter('trim');
        return $this;
    }

    function filter_xss() {
        $this->element->addFilter('Security::xss_clean');
        return $this;
    }

    function filter_abs() {
        $this->element->addFilter('abs');
        return $this;
    }

}