const fs = require('fs');
const html = fs.readFileSync('rendered2.html', 'utf8');
const regex = /<script>([\s\S]*?)<\/script>/g;
let match;
while ((match = regex.exec(html)) !== null) {
    try {
        new Function(match[1]);
        console.log('Valid script');
    } catch (e) {
        console.error('INVALID SCRIPT!', e);
        fs.writeFileSync('failed_script.js', match[1]);
    }
}
