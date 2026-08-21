import { chromium } from 'playwright';

async function runVisualTests() {
    console.log('Launching Chromium browser for visual testing...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 }
    });
    const page = await context.newPage();

    const artifactDir = '/Users/kalam/.gemini/antigravity-ide/brain/36f4d5cb-83f7-496b-be86-caea71328d2d';

    // 1. Visit Home Landing Page
    console.log('1. Loading http://127.0.0.1:8000 ...');
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
    await page.screenshot({ path: `${artifactDir}/01_landing_page.png`, fullPage: false });
    console.log('Saved: 01_landing_page.png');

    // 2. Click "Super Admin" Demo Button
    console.log('2. Clicking Super Admin Demo Auto-fill...');
    await page.click('button:has-text("Super Admin")');
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/02_demo_credentials_filled.png` });
    console.log('Saved: 02_demo_credentials_filled.png');

    // 3. Click "Sign In to CRM" and wait for Session Panel
    console.log('3. Submitting Sign In form...');
    await page.click('#loginSubmitBtn');
    await page.waitForSelector('#sessionPanel:not([style*="display: none"])', { timeout: 5000 });
    await page.waitForTimeout(600);
    await page.screenshot({ path: `${artifactDir}/03_authenticated_session.png` });
    console.log('Saved: 03_authenticated_session.png');

    // 4. Click "Sign Out" and Switch to "Create Tenant" Tab
    console.log('4. Testing Sign Out and Registration tab...');
    await page.click('button:has-text("Sign Out")');
    await page.waitForTimeout(400);
    await page.click('#tabRegisterBtn');
    await page.waitForTimeout(300);

    // Fill registration form
    await page.fill('#regOrg', 'Nova Quantum Dynamics');
    await page.fill('#regFirstName', 'Marcus');
    await page.fill('#regLastName', 'Vance');
    await page.fill('#regEmail', 'marcus.vance@novaquantum.io');
    await page.fill('#regPassword', 'Password123!');
    await page.fill('#regPasswordConfirm', 'Password123!');

    await page.screenshot({ path: `${artifactDir}/04_registration_form.png` });
    console.log('Saved: 04_registration_form.png');

    await browser.close();
    console.log('Visual test suite completed successfully!');
}

runVisualTests().catch(err => {
    console.error('Visual test failed:', err);
    process.exit(1);
});
