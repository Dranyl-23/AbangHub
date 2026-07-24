const fs = require('fs');

const replacements = [
    { file: 'resources/views/admin/users/index.blade.php', find: 'Storage::url()', replace: 'Storage::url($user->profile_image)' },
    { file: 'resources/views/dashboard/landlord.blade.php', find: 'Storage::url()', replace: 'Storage::url($lease->tenant->profile_image)' },
    { file: 'resources/views/host/show.blade.php', find: 'Storage::url()', replace: 'Storage::url($user->profile_image)' },
    { file: 'resources/views/landlord/maintenance/index.blade.php', find: 'Storage::url()', replace: 'Storage::url($request->user->profile_image)' },
    { file: 'resources/views/messages/index.blade.php', find: 'Storage::url()', replace: 'Storage::url($otherUser->profile_image)' },
    { file: 'resources/views/messages/show.blade.php', find: 'Storage::url()', replace: 'Storage::url($user->profile_image)' }, // Will replace all occurrences
    { file: 'resources/views/profile/edit.blade.php', find: 'Storage::url()', replace: 'Storage::url($user->profile_image)' },
    { file: 'resources/views/properties/show.blade.php', find: 'Storage::url()', replace: 'Storage::url($review->tenant->profile_image)' },
    { file: 'resources/views/properties/show.blade.php', find: 'Storage::url()', replace: 'Storage::url($property->owner->profile_image)' }
];

replacements.forEach(({ file, find, replace }) => {
    let content = fs.readFileSync(file, 'utf8');
    // We can't just replace all in properties/show because there are two different replacements.
    // Wait, properties/show has two different variables. 
    // Let's do it carefully by reading lines.
});
