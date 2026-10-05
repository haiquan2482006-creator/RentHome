const fs = require('fs');
let code = fs.readFileSync('e:/renthome/RentHome/resources/views/Home.blade.php', 'utf8');
const scripts = [...code.matchAll(/<script>([\s\S]*?)<\/script>/gi)];
let script = scripts[1][1];
script = script.replace(/const IS_ADMIN_USER = @json\(Auth::check\(\)[\s\S]*?\)\);/g, 'const IS_ADMIN_USER = false;');
script = script.replace(/\{\{(.*?)\}\}/g, '\"\"');
script = script.replace(/@php([\s\S]*?)@endphp/g, '');
script = script.replace(/@auth|@endauth|@if.*?|@else|@endif/g, '');
fs.writeFileSync('temp8.js', script);
