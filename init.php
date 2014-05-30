<?php
    //обработка события инициализации QuickForm
    //именно на это событие надо регистрировать новые типы элементов форм и рендереров
    \Meerkat\Event\Event::dispatcher()
        ->connect('QUICKFORM', function (sfEvent $event) {
            HTML_QuickForm2_Factory::registerElement('group_actions', 'HTML_QuickForm2_Group_Actions', Kohana::find_file('include', 'HTML/QuickForm2/Group/Actions'));
        });