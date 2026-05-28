const fs = require('fs');
const html = fs.readFileSync('rendered2.html', 'utf8');
const regex = /<script>([\s\S]*?)<\/script>/g;
let match;
let i = 1;
while ((match = regex.exec(html)) !== null) {
    fs.writeFileSync('script' + i + '.js', match[1]);
    console.log('Saved script' + i + '.js');
    i++;
}
