<?php
require_once(__DIR__ . '/../../config.php');

$tab = optional_param('tab', 'dashboard', PARAM_ALPHA);
$subtab = optional_param('subtab', 'courses', PARAM_ALPHA);

$url = new moodle_url('/local/admin_panel/index.php', ['tab' => $tab]);
if ($tab === 'courses') {
    $url->param('subtab', $subtab);
}
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'local_admin_panel'));
$PAGE->set_heading(get_string('pluginname', 'local_admin_panel'));

// Añadir clase para controlar el ancho de la página
$PAGE->add_body_class('local-admin-panel-fullwidth');

require_login();
require_capability('moodle/site:config', context_system::instance());

// Configurar el breadcrumb (navbar) manualmente
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('localplugins'));
$PAGE->navbar->add(get_string('pluginname', 'local_admin_panel'), new moodle_url('/local/admin_panel/index.php'));

if ($tab !== 'dashboard') {
    $PAGE->navbar->add(get_string('tab_' . $tab, 'local_admin_panel'));
}

// Cargar estilos CSS unificados
$PAGE->requires->css('/local/admin_panel/styles.css');

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_admin_panel');

if ($tab === 'dashboard') {
    $content = $renderer->render(new \local_admin_panel\output\dashboard_panel());
} elseif ($tab === 'courses') {
    if ($subtab === 'courses') {
        $sort = optional_param('sort', 'timecreated', PARAM_ALPHA);
        $dir = optional_param('dir', 'DESC', PARAM_ALPHA);
        $page = optional_param('page', 0, PARAM_INT);
        $search = optional_param('search', '', PARAM_TEXT);
        $category = optional_param('category', 0, PARAM_INT);
        $visibility = optional_param('visibility', -1, PARAM_INT);
        $subcontent = $renderer->render(new \local_admin_panel\output\courses_list($sort, $dir, $page, $search, $category, $visibility));
    } else {
        $subcontent = $renderer->render(new \local_admin_panel\output\categories_list());
    }
    $content = $renderer->render(new \local_admin_panel\output\courses_layout($subtab, $subcontent));
} elseif ($tab === 'users') {
    $content = $renderer->render(new \local_admin_panel\output\users_panel());
} elseif ($tab === 'cohorts') {
    $content = $renderer->render(new \local_admin_panel\output\cohorts_panel());
} else {
    $content = '';
}

$main_page = new \local_admin_panel\output\main($tab, $content);
echo $renderer->render($main_page);

echo $OUTPUT->footer();
