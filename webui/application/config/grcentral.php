<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//
// General settings
//

// ENG: The main title that will be added to all pages
// RUS: Главный тайтл который будет добавляться ко всем страницам
$config['grcentral']['site_title'] 				= 'GRCentral';

// ENG: Path to the file storage
// RUS: Путь к хранилищу файлов
$config['grcentral']['storage_path']			= $_SERVER['DOCUMENT_ROOT'].'storage/';

//
// Provisioning
//

// ENG: Automatically adding devices to the database when accessing the server (CFG request)
// RUS: Автоматическое добавление девайсов в базу данных при обращении к серверу (CFG запрос)
$config['provisioning']['auto_add_devices']		= TRUE; // TRUE or FALSE