<?php

define('DS', DIRECTORY_SEPARATOR);

define('ROOT', dirname(dirname(__FILE__)));

define('API_DIR', "{$_SERVER['DOCUMENT_ROOT']}/api/citsk/application");

define('JWT', [
    'iss'        => 'polls.nadzor26.ru',
    'aud'        => 'polls.nadzor26.ru',
    'iat'        => 1590008094,
    'nbf'        => 1590008094,
    'secret_key' => "|mIIGLasfLZ*fGiYXlb|^1PO2=ZTa;SHXngx1SW()&dtW3:rz_vB;vb]nTP49-NbH0`0",
]);
