import { chromium } from 'playwright';

const browser = await chromium.launch({ executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe' });
const page = await browser.newPage();
await page.setViewportSize({ width: 1440, height: 900 });
await page.goto('http://localhost:8080', { waitUntil: 'networkidle', timeout: 30000 });
await page.waitForTimeout(3000);
await page.screenshot({ path: 'screenshot.png', fullPage: false });
await browser.close();
console.log('Screenshot saved.');
