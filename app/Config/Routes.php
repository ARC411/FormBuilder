<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
// 1. AUTHENTICATION & DEFAULT
// ==========================================================
$routes->get('/', 'Auth::index', ['priority' => 1]);
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/forbidden', 'Auth::forbidden');
$routes->get('auth/cek_role', 'Auth::cek_role');
$routes->cli('cronjob/closeweeklydata', 'Cronjob::closeWeeklyData');

// ==========================================================
// 2. CORE MODULES: PRODUCTION
// ==========================================================
$routes->get('/production', 'Production::index');
$routes->get('/production/datatable', 'Production::datatable');
$routes->get('/production/data', 'Production::dataProduction');
$routes->get('/production/form', 'Production::formInputProduction');
$routes->post('/production', 'Production::createProduction');
$routes->post('/production/update', 'Production::createProduction');
$routes->get('/production/edit/form', 'Production::formEdit');
$routes->get('/production/edit/(:num)', 'Production::edit/$1');
$routes->get('/productions/history/(:num)', 'Production::productionByNumber/$1');
$routes->delete('/production/(:num)', 'Production::deleteProduction/$1');

// ==========================================================
// 3. CORE MODULES: STARTUP
// ==========================================================
$routes->get('/startup', 'Startup::index');
$routes->get('/startup/data', 'Startup::dataStartup');
$routes->get('/startup/form', 'Startup::formInputStartup');
$routes->post('/startup', 'Startup::createStartup');
$routes->post('/startup/create', 'Startup::createStartup');
$routes->post('/startup/update', 'Startup::createStartup');
$routes->post('/startup/update/(:num)', 'Startup::createStartup');
$routes->get('/startup/edit/form', 'Startup::formEdit');
$routes->get('/startup/edit/(:num)', 'Startup::edit/$1');
$routes->get('/startup/history/(:num)', 'Startup::startupByNumber/$1');
$routes->get('/startup/startupByNumber/(:num)', 'Startup::startupByNumber/$1');
$routes->delete('/startup/(:num)', 'Startup::deleteStartup/$1');
$routes->post('startup/request-edit/(:num)', 'Startup::requestEdit/$1');
$routes->post('startup/unlock-edit/(:num)', 'Startup::unlockEdit/$1');

// ==========================================================
// 4. CORE MODULES: FOREGOING
// ==========================================================
$routes->get('/foregoing', 'Foregoing::index');
$routes->get('/foregoing/data', 'History::dataForegoing');
$routes->get('/foregoing/form', 'Foregoing::formInputForegoing');
$routes->post('/foregoing', 'Foregoing::createForegoing');
$routes->post('/foregoing/create', 'Foregoing::createForegoing');
$routes->post('/foregoing/update', 'Foregoing::createForegoing');
$routes->post('/foregoing/update/(:num)', 'Foregoing::createForegoing');
$routes->get('/foregoing/edit/form', 'Foregoing::formEdit');
$routes->get('/foregoing/edit/(:num)', 'Foregoing::edit/$1');
$routes->get('/foregoing/history/(:num)', 'History::foregoingByNumber/$1');
$routes->delete('/foregoing/(:num)', 'Foregoing::deleteForegoing/$1');
$routes->post('foregoing/request-edit/(:num)', 'Foregoing::requestEdit/$1');
$routes->post('foregoing/unlock-edit/(:num)', 'Foregoing::unlockEdit/$1');

// ==========================================================
// 5. GENERAL FEATURES (Input, History, Log Activity)
// ==========================================================
$routes->get('/home', 'LotHistory::index');
$routes->get('/log-activity', 'LogActivity::index');
$routes->get('/input', 'Input::index');
$routes->get('/input/note-form/(:any)', 'Input::formStandard/$1');
$routes->get('/history', 'History::index');
$routes->get('/history/export/ff-d2-001', 'History::exportFF_D2_001');
$routes->get('history/revisions', 'History::getRevisions');
$routes->get('history/data', 'History::getDataTable');

// ==========================================================
// 6. APPROVAL SYSTEM
// ==========================================================
$routes->get('/approve', 'Approval::index');
$routes->get('/approve-form', 'Approval::formApproval');
$routes->match(['post', 'put', 'patch'], '/approve', 'Approval::updateApproval');
$routes->match(['post', 'put', 'patch'], '/approve/(:any)', 'Approval::updateApproval');
$routes->get('/approval/history/(:any)', 'Approval::approvalById/$1');
$routes->get('/approval/weekly', 'Approval::weekly');
$routes->post('/approval/processWeekly', 'Approval::processWeekly');
$routes->get('/approval/detail_mingguan/(:num)', 'Approval::detail_mingguan/$1');
$routes->get('/approval/weekly_form/(:segment)', 'Approval::weekly_form/$1');
$routes->get('/approval/([a-zA-Z0-9]+)', 'Home::approval/$1');

// ==========================================================
// 7. MASTER DATA: DEVICE
// ==========================================================
$routes->get('/device', 'Device::index');
$routes->get('/device/list', 'Device::listDevice');
$routes->get('/device/add', 'Device::createDeviceForm');
$routes->post('/device', 'Device::createDevice');
$routes->get('/device/edit/(:num)', 'Device::updateDeviceForm/$1');
$routes->post('/device/update', 'Device::updateDevice');
$routes->delete('/device/([0-9]+)', 'Device::deleteDevice/$1');

// ==========================================================
// 8. MASTER DATA: PROCESS
// ==========================================================
$routes->get('/process', 'Process::index');
$routes->get('/process/list', 'Process::listProcess');
$routes->get('/process/add', 'Process::addProcess');
$routes->post('/process', 'Process::createProcess');
$routes->get('/process/edit/(:num)', 'Process::updateProcessForm/$1');
$routes->post('/process/update', 'Process::updateProcess');
$routes->delete('/process/([0-9]+)', 'Process::deleteProcess/$1');

// ==========================================================
// 9. MASTER DATA: MACHINE LIST
// ==========================================================
$routes->get('/machine', 'Machine::index');
$routes->get('/machine/list', 'Machine::getList');
$routes->get('/machine/get-processes', 'Machine::getProcessesByDevice');
$routes->post('/machine/save', 'Machine::save');
$routes->get('/machine/edit/(:num)', 'Machine::editMachineForm/$1');
$routes->post('/machine/update', 'Machine::updateMachine');
$routes->delete('/machine/(:num)', 'Machine::deleteMachine/$1');

// ==========================================================
// 10. EXPORT & DATA CLEANSING
// ==========================================================
$routes->get('/history/exportExcel', 'History::exportExcel');
$routes->get('/history/exportPDF', 'History::exportPDF');
$routes->get('/cleansing', 'Cleansing::index');
$routes->post('/cleansing/execute', 'Cleansing::execute');

// ==========================================================
// 11. TESTING & UTILITIES
// ==========================================================
$routes->match(['GET', 'POST'], 'home/ajaxAutofill', 'Home::ajaxAutofill');
$routes->get('/testing', 'Test::testEmp');
$routes->get('/test2/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)', 'Test::testStartup/$1/$2');

// ==========================================================
// 12. FORM BUILDER (DYNAMIC PARAMETER)
// ==========================================================
$routes->get('form-builder', 'FormBuilder::index');
$routes->get('form-builder/get-processes', 'FormBuilder::getProcesses');
$routes->post('form-builder/load', 'FormBuilder::loadForm');
$routes->post('form-builder/save', 'FormBuilder::saveParameter');
$routes->post('form-builder/save-tree', 'FormBuilder::saveTreeParameter');
$routes->post('form-builder/delete', 'FormBuilder::deleteParameter');
$routes->post('form-builder/up-revisi', 'FormBuilder::upRevisi');
$routes->post('form-builder/row-save', 'FormBuilder::saveMatrixRow');
$routes->post('form-builder/row-delete', 'FormBuilder::deleteMatrixRow');
$routes->post('form-builder/row-move', 'FormBuilder::moveMatrixRow');
$routes->match(['GET', 'POST'], 'form-builder/render-input', 'FormBuilder::renderOperatorForm');

// ==========================================================
// 13. MASTER DATA: MODEL LIST
// ==========================================================
$routes->get('model/list', 'ModelData::getList');
$routes->post('model/auto-register', 'ModelData::autoRegister');
$routes->post('model/save', 'ModelData::saveModel');
$routes->post('model/save-standards', 'ModelData::saveStandards');
$routes->post('model/delete', 'ModelData::deleteModel');