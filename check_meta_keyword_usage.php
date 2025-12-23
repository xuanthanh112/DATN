<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING META_KEYWORD USAGE ===" . PHP_EOL . PHP_EOL;

// Check all tables with meta_keyword column
$tables = [
    'posts',
    'post_catalogue_language',
    'post_language',
    'products',
    'product_catalogue_language',
    'product_language',
    'attributes',
    'attribute_catalogue_language',
    'attribute_language',
    'languages',
    'systems',
];

echo "TABLES WITH meta_keyword COLUMN:" . PHP_EOL;
foreach($tables as $table) {
    try {
        $exists = DB::getSchemaBuilder()->hasColumn($table, 'meta_keyword');
        if($exists) {
            $count = DB::table($table)->whereNotNull('meta_keyword')->where('meta_keyword', '!=', '')->count();
            echo "✅ {$table}: HAS COLUMN - {$count} records with non-empty meta_keyword" . PHP_EOL;
        } else {
            echo "❌ {$table}: NO COLUMN" . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo "⚠️  {$table}: Table not exists or error" . PHP_EOL;
    }
}

echo PHP_EOL . "=== CHECKING SYSTEM SETTINGS ===" . PHP_EOL . PHP_EOL;

$systemSettings = DB::table('systems')->where('keyword', 'like', '%meta_keyword%')->get();
foreach($systemSettings as $setting) {
    echo "System setting: {$setting->keyword} = {$setting->content}" . PHP_EOL;
}

echo PHP_EOL . "=== SUMMARY ===" . PHP_EOL;
echo "Total tables checked: " . count($tables) . PHP_EOL;

