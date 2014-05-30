<?php

    namespace Meerkat\Form;
    use Meerkat\Html\Icon_Famfamfam;

    use \Debug as Debug;
    use Meerkat\Form\Element_Button;
    use Meerkat\Form\Element_Checkbox;
    use Meerkat\Form\Element_Date;
    use Meerkat\Form\Element_File;
    use Meerkat\Form\Element_Hidden;
    use Meerkat\Form\Element_HierSelect;
    use Meerkat\Form\Element_InputButton;
    use Meerkat\Form\Element_Image;
    use Meerkat\Form\Element_Password;
    use Meerkat\Form\Element_Radio;
    use Meerkat\Form\Element_Repeat;
    use Meerkat\Form\Element_Recaptcha;
    use Meerkat\Form\Element_Reset;
    use Meerkat\Form\Element_Select;
    use Meerkat\Form\Element_Static;
    use Meerkat\Form\Element_Submit;
    use Meerkat\Form\Element_Text;
    use Meerkat\Form\Element_Textarea;

    class Container extends Node {

        static function rule_thumb_url($val) {
            return !$val || filter_var($val, FILTER_VALIDATE_URL);
        }

        //##########################################################################
        // Группы
        //##########################################################################

        /**
         * @param type $element
         * @return \Meerkat\Form\Container
         */
        static function factory($element) {
            return new Container($element);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Container
         */
        function set_count_columns($columns) {
            $this->element->columns = (int)$columns;
            return $this;
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Container
         */
        function add_group($name = '') {
            $el = $this->element
                ->addGroup($name)
                ->addClass('form-group');
            return Container::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Container
         */
        function add_repeat($name = '') {
            $el = $this->element->addRepeat($name);
            return Container::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_HierSelect
         */
        function add_hierselect($name) {
            $el = $this->element->addHierselect($name);
            return Element_HierSelect::factory($el);
        }


        //##########################################################################
        // Элементы
        //##########################################################################

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Image
         */
        function add_float($name) {
            $el = $this->add_text($name);
            $el->rule_regexp('/^[-+]?[0-9]+(\.[0-9]+)?$/', 'Только числа (целые и дробные)');
            return $el;
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Text
         */
        function add_text($name) {
            $el = $this->element->addText($name);
            return Element_Text::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Node
         */
        function add_hidden($name) {
            $el = $this->element->addHidden($name);
            return Element_Hidden::factory($el);
        }

        function rule_thumb($val) {
            return isset($_FILES['picture']) || Arr::get($_POST, 'url');
        }

        function add_thumb($model, $legend = 'Иллюстрация', $prop = null) {
            $name = $model->object_name();
            if ($prop) {
                $name .= '_' . $prop;
            }
            $fieldset = $this->add_fieldset($legend);

            $upload = $fieldset
                ->add_file($name . '_file', 104.8576)
                ->set_label('Загрузить с диска')
                //->add_class('form-control')
                ->set_desc('Разрешенные расширения: *.gif, *.jpeg, *.jpg, *.png');
            $upload->rule_img_extensions();
            $upload->rule_img_mime();

            $el_url = $fieldset
                ->add_text($name . '_url')
                ->set_label('Загрузить по ссылке')
                ->set_prepend('<i class="iconfam_world_link"></i>')
                ->add_class('form-control')
                ->set_desc('Скопируйте в это поле ссылку на понравившуюся картинку');
            $el_url
                ->get_element()
                ->addRule('callback', 'Не корректная ссылка', array(
                    'callback' => array('\Meerkat\Form\Container',
                        'rule_thumb_url'),
                ));
            $el_url = $fieldset
                ->add_checkbox($name . '_del')
                ->set_label('Удалить изображения?');
            if ($model->pk()) {
                $thumb = \Meerkat\Thumb\Thumb::factory($model->object_name(), $model->pk(), $prop);
                $thumbs = array();
                foreach ($thumb->get_size_names() as $name) {
                    if ($name != \Meerkat\Thumb\Thumb::RAW) {
                        $thumbs[] = $thumb
                            ->img($name, null, true)
                            ->addClass('img-thumbnail');
                    }
                }
                $fieldset->add_static(implode('',$thumbs));
            }
            /* $el_url->addRule('callback', 'Укажите хотя бы ссылку на картинку или загрузите ее с диска', array(
              'callback' => array('\Meerkat\Form\Container', 'rule_thumb'),
              )
              ); */
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Container
         */
        function add_fieldset($legend) {
            $el = $this->element->addFieldset();
            return Container::factory($el)
                ->set_label($legend);
        }

        /* function insert_file($name, $max = null, $max_error = '') {
          $upload = $this->addElement('file', $name);
          $form = $this->get_form();
          $form->setAttribute('enctype', 'multipart/form-data');
          if ($max) {
          //$form->addElement('hidden', 'MAX_FILE_SIZE')->setValue($max);
          //$upload->addRule('maxfilesize', 'Размер файла больше допустимого '.Text::bytes($max), $max);
          }
          return $upload;
          } */

        /**
         * @param type $name
         * @return \Meerkat\Form\Node
         */
        function add_file($name, $max = null, $max_error = '') {
            $el = $this->element->addFile($name);
            $this
                ->get_form()
                ->setAttribute('enctype', 'multipart/form-data');
            return Element_File::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Checkbox
         */
        function add_checkbox($name, $value = 1) {
            $el = $this->element->addCheckbox($name, array('value' => $value));
            return Element_Checkbox::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Static
         */
        function add_static($html) {
            $el = $this->element->addStatic();
            $el->setContent($html);
            return Element_Static::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Text
         */
        function add_email($name, $error = 'Не верный Email-адрес') {
            $el = $this->add_text($name);
            $el
                ->get_element()
                ->addRule('email', $error, null, \HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
            $el->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_EMAIL));
            return $el;
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Textarea
         */
        function add_textarea($name) {
            $el = $this->element->addTextarea($name);
            return Element_Textarea::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Date
         */
        function add_date($name, $data = array()) {
            $el = $this->add_text($name);
            \Meerkat\StaticFiles\Js::instance()
                ->need_jquery_ui();
            \Meerkat\StaticFiles\Js::instance()
                ->add_onload('
            jQuery("#' . $el
                    ->get_element()
                    ->getId() . '" ).datepicker({
                    dateFormat:"yy-mm-dd",
                    showOtherMonths: true,
                    numberOfMonths: 3,
                    changeMonth:true,
                    firstDay: 1,
                    changeYear:true,
                    monthNames: [\'Январь\', \'Февраль\', \'Март\', \'Апрель\', \'Май\', \'Июнь\',
                    \'Июль\', \'Август\', \'Сентябрь\', \'Октябрь\', \'Ноябрь\', \'Декабрь\'],
                    monthNamesShort: [\'Янв\', \'Фев\', \'Мар\', \'Апр\', \'Май\', \'Июн\',
                    \'Июл\', \'Авг\', \'Сен\', \'Окт\', \'Ноя\', \'Дек\'],
                    selectOtherMonths: true,
                    showButtonPanel: true
            });
        ');
            return Element_Date::factory($el);
        }


        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Date
         */
        function add_datetime($name, $data = array()) {
            $el = $this->add_text($name);
            \Meerkat\StaticFiles\Js::instance()
                ->need_jquery_ui();
            \Meerkat\StaticFiles\Js::instance()
                ->add_onload('
            jQuery("#' . $el
                    ->get_element()
                    ->getId() . '" ).datepicker({
                    dateFormat:"yy-mm-dd",
                    showOtherMonths: true,
                    numberOfMonths: 3,
                    changeMonth:true,
                    firstDay: 1,
                    changeYear:true,
                    monthNames: [\'Январь\', \'Февраль\', \'Март\', \'Апрель\', \'Май\', \'Июнь\',
                    \'Июль\', \'Август\', \'Сентябрь\', \'Октябрь\', \'Ноябрь\', \'Декабрь\'],
                    monthNamesShort: [\'Янв\', \'Фев\', \'Мар\', \'Апр\', \'Май\', \'Июн\',
                    \'Июл\', \'Авг\', \'Сен\', \'Окт\', \'Ноя\', \'Дек\'],
                    selectOtherMonths: true,
                    showButtonPanel: true
            });
        ');
            return Element_Date::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Radio
         */
        function add_radio($name, $value = null) {
            $el = $this->element->addRadio($name, array('value' => $value));

            //$el = $this->element->addRadio($name);
            return Element_Radio::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Reset
         */
        function add_recaptcha() {
            $el = $this->add_static(\Meerkat\Google\Recaptcha::get_html());
            return Element_Recaptcha::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Reset
         */
        function add_reset($name) {
            $el = $this->element->addReset($name);
            return Element_Reset::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Select
         */
        function add_select($name) {
            $el = $this->element->addSelect($name);
            return Element_Select::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Password
         */
        function add_password($name) {
            $el = $this->element->addPassword($name);
            return Element_Password::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Button
         */
        function add_button($name, $content) {
            $el = $this->element->addButton($name, null, array('content' => $content));
            return Element_Button::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Container
         */
        function add_actions_group($name = 'submit-group') {
            $el = $this->element->addElement('group_actions', $name);
            return Container::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_InputButton
         */
        function add_inputbutton($name) {
            $el = $this->element->addInputButton($name);
            return Element_InputButton::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Image
         */
        function add_image($name) {
            $el = $this->element->addImage($name);
            return Element_Image::factory($el);
        }

        /**
         * @param type $name
         * @return \Meerkat\Form\Element_Image
         */
        function add_int($name) {
            $el = $this->add_text($name);
            $el->rule_regexp('/^[0-9]+$/', 'Только целые значения');
            return $el;
        }

        /**
         *
         * @param type $name
         * @return \Meerkat\Form\Element_Submit
         */
        function add_submit($label, $name = '') {
            $el = $this->element->addSubmit($name);
            return Element_Submit::factory($el)
                ->set_label($label);
        }

        function tb_submit($label, $name = '') {
            return $this
                ->add_submit($label, $name)
                ->add_class('btn');
        }

        function tb_text($name) {
            return $this
                ->add_text($name)
                ->add_class('form-control');
        }

        function tb_textarea($name) {
            return $this
                ->add_textarea($name)
                ->add_class('form-control');
        }

        function tb_select($name) {
            return $this
                ->add_select($name)
                ->add_class('form-control');
        }

    }