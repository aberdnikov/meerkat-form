<?php

    namespace Meerkat\ORMForm;

    use Meerkat\User\Me;

    class Kohana_ORMForm_User extends ORMForm {

        function field__email($container) {
            $container
                ->add_email('email')
                ->set_label('E-mail')
                ->rule_required()
                ->rule_callback(array($this,
                    'check_email'), 'На проекте уже есть участник с таким электронным адресом')
                ->add_class('form-control')
                ->set_desc('');
        }

        function check_email($email) {
            return $this->_model->unique('email', $email);
        }

        function field__username($container) {
            $container
                ->add_text('username')
                ->set_label('Имя')
                ->rule_required()
                ->add_class('form-control');
        }

        function field__about($container) {
            $el = $container
                ->add_textarea('about')
                ->set_label('О себе')
                ->set_attribute('rows', 20)
                ->filter_xss()
                ->set_attribute('style', 'width:100%')
                ->set_desc('Здесь можете подробно описать о себе, будет выводиться на вашей персональной странице')
                ->add_class('form-control');
            $el->add_class('ckeditor');
            \Meerkat\StaticFiles\Js::instance()
                ->add(URL_CKEDITOR . 'ckeditor.js');
        }

        function field__is_man($container) {
            $gr = $container
                ->add_group()
                ->set_label('Пол');
            foreach (\Kohana_Model_User::$gender_text as $k => $v) {
                $gr
                    ->add_radio('is_man', $k)
                    ->is_inline(1)
                    ->set_label('<i class="' . \Arr::get(\Kohana_Model_User::$gender_ico, $k) . '"></i> ' . $v);
            }
        }

        function field__login($container) {
            $el = $container
                ->add_text('login')
                ->add_class('form-control')
                ->set_label('Логин')
                ->rule_callback(array($this,
                    'check_login'), 'На проекте уже есть участник с таким логином')
                ->rule_maxlength(255, 'Не длинее 255 символов')
                ->set_append(\Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER))
                ->set_example('ivanov')
                ->set_example('alexey')
                ->set_example('nickname')
                ->rule_required()
                ->filter_xss()
                ->set_example('nissan')
                ->set_desc('Допустимы латинские буквы, цифры, тире и знак подчеркивания. ')
                ->rule_regexp('/^[a-z0-9-]+$/', 'Допускаются только латинские буквы, цифры, тире и знак подчеркивания');
            if (\Kohana::$config->load('meerkat/user.url.public_users')) {
                $el->set_prepend('http://' . $_SERVER['HTTP_HOST'] . \Kohana::$config->load('meerkat/user.url.public_users'));
            }
            return $el;
        }

        function check_login($email) {
            return $this->_model->unique('login', $email);
        }

    }