<?php
$user = \App\Models\User::where('email', 'alfielynard23@gmail.com')->first();
if ($user) {
    $user->password = bcrypt('password123');
    $user->save();
    echo "SUCCESS: Password updated.\n";
} else {
    echo "NOT FOUND.\n";
}
