<?php return array (
  'barryvdh/laravel-dompdf' => 
  array (
    'providers' => 
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Pdf' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
      'PDF' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
    ),
  ),
  'facade/ignition' => 
  array (
    'providers' => 
    array (
      0 => 'Facade\\Ignition\\IgnitionServiceProvider',
    ),
    'aliases' => 
    array (
      'Flare' => 'Facade\\Ignition\\Facades\\Flare',
    ),
  ),
  'fruitcake/laravel-cors' => 
  array (
    'providers' => 
    array (
      0 => 'Fruitcake\\Cors\\CorsServiceProvider',
    ),
  ),
  'kreait/laravel-firebase' => 
  array (
    'providers' => 
    array (
      0 => 'Kreait\\Laravel\\Firebase\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Firebase' => 'Kreait\\Laravel\\Firebase\\Facades\\Firebase',
      'FirebaseAuth' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseAuth',
      'FirebaseDatabase' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseDatabase',
      'FirebaseDynamicLinks' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseDynamicLinks',
      'FirebaseFirestore' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseFirestore',
      'FirebaseMessaging' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseMessaging',
      'FirebaseRemoteConfig' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseRemoteConfig',
      'FirebaseStorage' => 'Kreait\\Laravel\\Firebase\\Facades\\FirebaseStorage',
    ),
  ),
  'laravel-notification-channels/fcm' => 
  array (
    'providers' => 
    array (
      0 => 'NotificationChannels\\Fcm\\FcmServiceProvider',
    ),
  ),
  'laravel/sail' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Sail\\SailServiceProvider',
    ),
  ),
  'laravel/sanctum' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Sanctum\\SanctumServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'maatwebsite/excel' => 
  array (
    'providers' => 
    array (
      0 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
    ),
    'aliases' => 
    array (
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'rap2hpoutre/fast-excel' => 
  array (
    'providers' => 
    array (
      0 => 'Rap2hpoutre\\FastExcel\\Providers\\FastExcelServiceProvider',
    ),
  ),
  'spatie/laravel-backup' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Backup\\BackupServiceProvider',
    ),
  ),
  'tucker-eric/eloquentfilter' => 
  array (
    'providers' => 
    array (
      0 => 'EloquentFilter\\ServiceProvider',
    ),
  ),
);