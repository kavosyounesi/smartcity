import { chromium } from 'playwright';

const browser = await chromium.launch({
  executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
});
const page = await browser.newPage();

page.on('console', msg => { if (msg.type() === 'error') console.log(`[error] ${msg.text().substring(0, 200)}`); });
page.on('pageerror', err => console.log('PAGE ERROR:', err.message.substring(0, 200)));

await page.setViewportSize({ width: 1440, height: 900 });
await page.goto('http://localhost:8080', { waitUntil: 'networkidle', timeout: 30000 });
await page.waitForTimeout(4000);

const rootChildren = await page.evaluate(() => document.getElementById('root')?.children.length);
console.log('Root children:', rootChildren);

await page.screenshot({ path: 'screenshot.png' });
await browser.close();
console.log('Done.');
