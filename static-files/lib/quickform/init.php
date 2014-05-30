<?php

use Meerkat\StaticFiles\Js;
use Meerkat\StaticFiles\Css;

Css::instance()->add_static('lib/quickform/css/quickform.css');
Js::instance()->add_static('lib/quickform/js/quickform.js');
Js::instance()->add_static('lib/quickform/js/quickform-hierselect.js');
Js::instance()->add_static('lib/quickform/js/quickform-repeat.js');