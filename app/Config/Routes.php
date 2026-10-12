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
$routes->cli('cronjob/closeweeklydata', 'Cronjob::closeWeeklyData');

$routes->get('auth/cek_role', 'Auth::cek_role');

// ==========================================================
// 2. CORE MODULES: PRODUCTION
// ==========================================================
$routes->get('/production', 'Production::index'); 
$routes->get('/production/datatable', 'Production::datatable'); 
$routes->get('/production/data','Production::dataProduction');
$routes->get('/production/form','Production::formInputProduction');
$routes->post('/production', 'Production::createProduction'); 
$routes->get('/production/edit/form', 'Production::formEdit');
$routes->get('/production/edit/(:num)', 'Production::edit/$1');
$routes->get('/productions/history/(:num)', 'Production::productionByNumber/$1');
$routes->delete('/production/(:num)','Production::deleteProduction/$1');

// ==========================================================
// 3. CORE MODULES: STARTUP
// ==========================================================
$routes->get('/startup', 'Startup::index'); 
$routes->get('/startup/data','Startup::dataStartup');
$routes->get('/startup/form','Startup::formInputStartup');
$routes->post('/startup', 'Startup::createStartup');
$routes->get('/startup/edit/form','Startup::formEdit');
$routes->get('/startup/edit/(:num)','Startup::edit/$1');
$routes->get('/startup/history/(:num)', 'Startup::startupByNumber/$1');
$routes->delete('/startup/(:num)','Startup::deleteStartup/$1');
$routes->post('/startup/update', 'Startup::updateStartup'); // Jika pakai route biasa
$routes->post('/startup/update/(:num)', 'Startup::updateStartup/$1'); // Jika pakai ID di URL

// ==========================================================
// 4. CORE MODULES: FOREGOING
// ==========================================================
$routes->get('/foregoing', 'Foregoing::index'); 
$routes->get('/foregoing/data','History::dataForegoing'); 
$routes->get('/foregoing/form','Foregoing::formInputForegoing');
$routes->post('/foregoing', 'Foregoing::createForegoing'); 
$routes->get('/foregoing/edit/form','Foregoing::formEdit'); 
$routes->get('/foregoing/edit/(:num)','Foregoing::edit/$1'); 
$routes->get('/foregoing/history/(:num)', 'History::foregoingByNumber/$1'); 
$routes->delete('/foregoing/(:num)','Foregoing::deleteForegoing/$1');

// ==========================================================
// 5. GENERAL FEATURES (Input, History, Log Activity)
// ==========================================================
$routes->get('/home', 'LotHistory::index');
$routes->get('/log-activity', 'LogActivity::index');
$routes->get('/input', 'Input::index'); 
$routes->get('/input/note-form/(:any)', 'Input::formStandard/$1');
$routes->get('/history', 'History::index');
$routes->get('/history/export/ff-d2-001', 'History::exportFF_D2_001');

// ==========================================================
// 6. APPROVAL SYSTEM
// ==========================================================
$routes->get('/approve', 'Approval::index');
$routes->get('/approve-form', 'Approval::formApproval');

// Rute Eksekusi Approve: Tangkap POST, PUT, dan PATCH, baik ada ID maupun tidak
// Kita hilangkan /$1 agar tidak bentrok dengan public function updateApproval() yang tidak punya parameter
$routes->match(['post', 'put', 'patch'], '/approve', 'Approval::updateApproval');
$routes->match(['post', 'put', 'patch'], '/approve/(:any)', 'Approval::updateApproval');

// Rute lainnya
$routes->get('/approval/history/(:any)', 'Approval::approvalById/$1');
$routes->get('/approval/weekly', 'Approval::weekly');
$routes->post('/approval/processWeekly', 'Approval::processWeekly');
$routes->get('/approval/detail_mingguan/(:num)', 'Approval::detail_mingguan/$1');
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
$routes->get('/machine/list', 'Machine::getList');
$routes->get('/machine/get-processes', 'Machine::getProcessesByDevice');
$routes->get('/machine', 'Machine::index');
$routes->post('/machine/save', 'Machine::save');
$routes->delete('/machine/(:num)', 'Machine::deleteMachine/$1');
$routes->get('/machine/edit/(:num)', 'Machine::editMachineForm/$1');
$routes->post('/machine/update', 'Machine::updateMachine');

// ==========================================================
// 10. EXPORT & DATA CLEANSING (Universal)
// ==========================================================
$routes->get('/history/exportExcel', 'History::exportExcel');
$routes->get('/history/exportPDF', 'History::exportPDF');
$routes->get('/cleansing', 'Cleansing::index');
$routes->post('/cleansing/execute', 'Cleansing::execute');

// ==========================================================
// 11. TESTING & UTILITIES
// ==========================================================
$routes->match(['GET', 'POST'], 'home/ajaxAutofill', 'Home::ajaxAutofill');
$routes->get('/testing','Test::testEmp');
$routes->get('/test2/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)','Test::testStartup/$1/$2');

// ==========================================================
// 12. RUTE CLEANSING DATA (Untuk Perbaikan Typo Machno Startup)
// ==========================================================
$routes->get('/cleansing', 'Cleansing::index');
$routes->post('/cleansing/execute', 'Cleansing::execute');

// ==========================================================
// 13. FORM BUILDER (DYNAMIC PARAMETER)
// ==========================================================
$routes->get('form-builder', 'FormBuilder::index');
$routes->get('form-builder/get-processes', 'FormBuilder::getProcesses');
$routes->post('form-builder/load', 'FormBuilder::loadForm');
$routes->post('form-builder/save', 'FormBuilder::saveParam');
$routes->post('form-builder/delete', 'FormBuilder::deleteParam');
$routes->post('form-builder/up-revisi', 'FormBuilder::upRevisi');

$routes->get('/run-migration', 'FormBuilder::eksekusiMigrasi');