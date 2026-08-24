<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = App\Models\User::where('email', 'admin@toolingroom.com')->first();
if ($u) {
    $u->password = Illuminate\Support\Facades\Hash::make('password');
    $u->save();
    echo "Password reset for " . $u->email . " to 'password'\n";
} else {
    // Nếu chưa có user thì tạo mới
    $u = new App\Models\User();
    $u->name = 'Admin';
    $u->email = 'admin@toolingroom.com';
    $u->password = Illuminate\Support\Facades\Hash::make('password');
    $u->save();
    echo "Created user admin@toolingroom.com with password 'password'\n";
}
