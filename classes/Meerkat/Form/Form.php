<?php

namespace Meerkat\Form;

require_once \Kohana::find_file('vendor','HTML/Common2');
require_once \Kohana::find_file('vendor','HTML/QuickForm2');
require_once \Kohana::find_file('vendor','HTML/QuickForm2/Renderer');

use \HTML_QuickForm2 as HTML_QuickForm2;
use \HTML_QuickForm2_Renderer as HTML_QuickForm2_Renderer;
use Meerkat\Twig\Twig;
use Meerkat\Slot\Slot_Thumb;

$event = new \sfEvent(null, 'QUICKFORM');
\Meerkat\Event\Event::dispatcher()->notify($event);


class Form extends Container {

    const LAYOUT_INLINE = 'form-inline';
    const LAYOUT_HORIZONTAL = 'form-horizontal';
    const LAYOUT_DEFAULT = 'form-default';

    protected $layout = self::LAYOUT_DEFAULT;
    protected $layouts = array(
        self::LAYOUT_DEFAULT => 'default',
        self::LAYOUT_HORIZONTAL => 'default',
        self::LAYOUT_INLINE => 'inline',
    );
    protected $classes = array(
        self::LAYOUT_DEFAULT,
        self::LAYOUT_HORIZONTAL,
        self::LAYOUT_INLINE,
    );

    /**
     * @param type $element
     * @return \Meerkat\Form\Form
     */
    static function factory($id, $method = 'post', $attributes = null, $trackSubmit = true) {
        $form = new HTML_QuickForm2($id, $method, $attributes, $trackSubmit);
        \Meerkat\StaticFiles\File::need_lib('quickform');
        return new Form($form);
    }

    function set_layout($layout) {
        if (in_array($layout, $this->classes)) {
            $this->layout = $layout;
            foreach ($this->classes as $class) {
                $this->element->removeClass($class);
            }
            $this->element->addClass($layout);
        }
        return $this;
    }

    protected $_auto_append_js = true;
    static public $_js = array();

    function auto_append_js($val = false) {
        $this->_auto_append_js = (bool) $val;
        return $this;
    }

    function get_js() {
        return \Arr::get(self::$_js, $this->get_form()->getId());
    }
    static function set_js($form_id, $js) {
        self::$_js[$form_id] = $js;
    }

    function set_action($url) {
        $this->element->setAttribute('action', $url);
        return $this;
    }

    function __toString() {
        try {
            return $this->render();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    function as_array() {
        HTML_QuickForm2_Renderer::register('meerkat', '\Meerkat\Form\Renderer', \Kohana::find_file('classes', 'Meerkat/Form/Renderer'));
        $renderer = HTML_QuickForm2_Renderer::factory('meerkat');
        $this->element->render($renderer);
        return $renderer->toArray();
    }

    function render($template = null) {
        if (!$template) {
            $template = '!/quickform/' . \Arr::get($this->layouts, $this->layout);
        }
        if ($this->layout) {
            $this->element->addClass($this->layout);
        }
        //\Debug::stop($template);
        $tpl = Twig::from_template($template);
        $tpl->set('form', $this->as_array());
        $tpl->set('layout', $this->layout);
        if ($this->_auto_append_js) {
            $script = $this->get_js();
            //\Debug::info($script);
            if ($script) {
                \Meerkat\StaticFiles\Js::instance()->add_inline($script, 'JS:'.$this->get_form()->getId());
            }
        }
        /* foreach( $renderer->getJavascriptBuilder()->getLibraries(true, false) as $p){
          Debug::stop($p);
          } */

        return $tpl->render();
    }

    function init_values($values) {
        //\Debug::stop((!$this->element->isSubmitted()));
        if (!$this->element->isSubmitted()) {
            $this->element->addDataSource(new \HTML_QuickForm2_DataSource_Array($values));
        }
    }

    function upload_thumb($model, $prop = null) {
        $name = $model->object_name();
        if ($prop) {
            $name .= '_' . $prop;
        }

        if (!($original = \Arr::path($_FILES, $name . '_file.tmp_name'))) {
            $original = \Arr::get($_POST, $name . '_url');
        }
        $thumb = \Meerkat\Thumb\Thumb::factory($model->object_name(), $model->pk(), $prop);
        if (isset($_POST[$name . '_del'])) {
            $thumb->delete();
        }
        $slot = Slot_Thumb::factory($model->object_name() . $model->pk() . $prop);
        $slot->remove();

        if ($original) {
            $model->reload();
            return $thumb->make($original);
        }
        return false;
    }

}