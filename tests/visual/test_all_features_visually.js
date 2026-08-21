import { chromium } from 'playwright';

async function captureAllFeatureScreenshots() {
    console.log('Starting Playwright automated visual test across all CRM features...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 }
    });
    const page = await context.newPage();

    const artifactDir = '/Users/kalam/.gemini/antigravity-ide/brain/36f4d5cb-83f7-496b-be86-caea71328d2d';

    // 1. Executive Dashboard
    console.log('1. Capturing Executive Sales Dashboard...');
    await page.goto('http://127.0.0.1:8000/app', { waitUntil: 'networkidle' });
    await page.waitForTimeout(600);
    await page.screenshot({ path: `${artifactDir}/01_executive_dashboard.png` });
    console.log('Saved: 01_executive_dashboard.png');

    // 2. Interactive Pipeline & Kanban Board
    console.log('2. Capturing Interactive Kanban Board...');
    await page.click('#navKanban');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/02_interactive_kanban.png` });
    console.log('Saved: 02_interactive_kanban.png');

    // 3. Leads Management & AI Qualification Scoring
    console.log('3. Capturing Leads & AI Qualification Scoring Engine...');
    await page.click('#navLeads');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/03_leads_ai_scoring.png` });
    console.log('Saved: 03_leads_ai_scoring.png');

    // 4. Companies & Enterprise Accounts
    console.log('4. Capturing Companies & Custom JSONB Attributes...');
    await page.click('#navCompanies');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/04_companies_accounts.png` });
    console.log('Saved: 04_companies_accounts.png');

    // 5. Contacts Roster & Decision Makers
    console.log('5. Capturing Key Contacts Roster...');
    await page.click('#navContacts');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/05_contacts_roster.png` });
    console.log('Saved: 05_contacts_roster.png');

    // 6. Activity Timeline & Spatie Audit Stream
    console.log('6. Capturing Activity Timeline & Audit Logs...');
    await page.click('#navTimeline');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/06_activity_timeline.png` });
    console.log('Saved: 06_activity_timeline.png');

    // 7. Global tsvector Full-Text Search
    console.log('7. Capturing PostgreSQL tsvector Ranked Search...');
    await page.click('#navSearch');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/07_global_search_results.png` });
    console.log('Saved: 07_global_search_results.png');

    await browser.close();
    console.log('All 7 CRM feature screenshots successfully captured!');
}

captureAllFeatureScreenshots().catch(err => {
    console.error('Visual capture failed:', err);
    process.exit(1);
});
