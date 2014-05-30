<?php

    namespace Meerkat\ORMForm;

    use \Arr;
    use \Debug;

    class ORMForm {

        protected $_structure;
        protected $_model;
        protected $_form;

        function __construct($model) {
            if ($model instanceof \ORM) {
                $this->_model = $model;
            }
            else {
                $this->_model = \ORM::factory($model);
            }
            $formname = $this->_model->object_name();
            if ($this->_model->pk()) {
                $formname .= '_' . $this->_model->pk();
            }
            $this->_form = \Meerkat\Form\Form::factory($formname);
        }

        /**
         * @param \Meerkat\ORMForm\ORM $model
         * @return \Meerkat\ORMForm\ORMForm
         */
        static function factory($model) {
            if ($model instanceof \ORM) {
                $class = '\Meerkat\ORMForm\ORMForm_' . \Text::ucfirst($model->object_name(), '_');
                if (!class_exists($class)) {
                    $class = __CLASS__;
                }
            }
            else {
                $class = __CLASS__;
            }

            return new $class($model);
        }

        function get_values() {
            return $this->_form
                ->get_element()
                ->getValue();
        }

        /**
         *
         * @return \Meerkat\Form\Form
         */
        function get_form() {
            return $this->_form;
        }

        /**
         *
         * @return \HTML_QuickForm2
         */
        function get_quickform() {
            return $this->_form->get_element();
        }

        function get_element($id) {
            return $this->_form
                ->get_element()
                ->getElementById($id);
        }

        /**
         * @param type $structure
         * @return \Meerkat\ORMForm\ORMForm
         */
        function set_structure($structure) {
            $this->_structure = $structure;
            return $this;
        }

        /*
          function field__xxx($container){

          }
         */

        function __toString() {
            try {
                return $this->_form->render();
            }
            catch (Exception $exc) {
                return \Kohana_Exception::text($exc);
            }
        }

        function build() {
            if (!$this->_structure) {
                $this->_structure = array_keys($this->_model->table_columns());
            }
            foreach ($this->_structure as $k => $v) {
                if (is_array($v)) {
                    $fieldset = $this->_form->add_fieldset($k);
                    foreach ($v as $field) {
                        $this->auto($field, $fieldset);
                    }
                }
                else {
                    $this->auto($v, $this->_form);
                }
            }
            return $this;
        }

        function auto($field_name, &$container) {
            $callback = 'field__' . $field_name;
            if (is_callable(array($this,
                $callback))
            ) {
                return call_user_func(array($this,
                    $callback), $container);
            }
            $field = Arr::get($this->_model->table_columns(), $field_name);
            if (!$field) {
                return false;
            }
            $type                     = Arr::get($field, 'type');
            $data_type                = Arr::get($field, 'data_type');
            $key                      = Arr::get($field, 'key');
            $is_nullable              = Arr::get($field, 'is_nullable');
            $character_maximum_length = (int)Arr::get($field, 'character_maximum_length');
            $label                    = str_replace('_', ' ', \Text::ucfirst($field_name, '_'));
            $label                    = $this->fieldLabel($field_name);
            //ключевые поля пропускаем
            if (Arr::get($field, 'key') == 'PRI') {
                return false;
            }
            //Debug::info($field);
            switch ($data_type) {
                case 'tinyint':
                    //$container = new \Meerkat\Form\Form($element);
                    $f = $container->add_checkbox($field_name);
                    break;
                case 'int':
                    $f = $container->add_int($field_name);
                    $f->add_class('form-control');
                    break;
                case 'float':
                case 'decimal':
                    $f = $container->add_float($field_name);
                    $f->add_class('form-control');
                    break;
                case 'varchar':
                    $f = $container->add_text($field_name);
                    if ($character_maximum_length) {
                        $f->rule_maxlength($character_maximum_length);
                    }
                    $f->add_class('form-control');
                    break;
                case 'text':
                    $f = $container->add_textarea($field_name);
                    $f->rule_maxlength($character_maximum_length);
                    $f->add_class('form-control');
                    break;
                case 'datetime':
                case 'date':
                    //$container = new \Meerkat\Form\Form($element);
                    $f = $container->add_date($field_name, array(
                        'format'  => 'dMY',
                        'minYear' => date('Y'),
                        'maxYear' => date('Y') + 10,
                        // set in the constructor
                    ));
                    break;
            }
            if (isset($f)) {
                $f->set_label($label);
                $f->filter_xss();
            }
        }

        function fieldLabel($field_name) {
            return $this->_model->label($field_name);
        }

        function init_values() {
            if ($this->_model->pk()) {
                $this->_form->init_values($this->_model->as_array());
            }
            if ($this->_model instanceof \Meerkat\ORMForm\ORMForm) {
                $this->_form->init_values($this->_model->meerkat->as_array());
            }
        }

        function as_vertical() {

        }

        function as_horizontal() {

        }

        function add_submit($label, $class = 'btn btn-primary btn-lg') {
            return $this->_form
                ->add_actions_group()
                ->add_submit('s')
                ->add_class($class)
                ->set_label($label);
        }

        function add_meta($legend = 'SEO') {
            $f = $this->_form->add_fieldset($legend);
            $k = $f
                ->add_text('meerkat_meta_keywords')
                ->set_label('Meta keywords')
                ->set_desc(htmlentities('<meta name="keywords" content="...">') . '<br />Опишите ключевыми фразами объект, до 20 фраз длиной не более 20 символов')
                ->set_example_ext('Ленин')
                ->set_example_ext('партия')
                ->set_example_ext('комсомол')
                ->add_class('form-control');
            $d = $f
                ->add_text('meerkat_meta_description')
                ->set_label('Meta description')
                ->set_desc(htmlentities('<meta name="description" content="...">'))
                ->add_class('form-control');
            \Meerkat\StaticFiles\File::need_lib('jquery-autocomplete');
            \Meerkat\StaticFiles\Js::instance()
                ->add_onload('
        $("#' . $k
                    ->get_element()
                    ->getId() . '").autocomplete({
            serviceUrl: "/!/autocomplete/keyword",
            delimiter: ","
        });');
        }

    }