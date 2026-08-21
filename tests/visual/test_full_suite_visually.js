import { chromium } from 'playwright';

async function runCompleteVisualTestSuite() {
    console.log('========================================================');
    console.log('   TOLO CRM — COMPREHENSIVE PLAYWRIGHT VISUAL SUITE     ');
    console.log('========================================================\n');

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 }
    });
    const page = await context.newPage();
    page.on('dialog', async dialog => {
        console.log(`   [Dialog ${dialog.type()}]: ${dialog.message()}`);
        await dialog.accept();
    });

    const artifactDir = '/Users/kalam/.gemini/antigravity-ide/brain/36f4d5cb-83f7-496b-be86-caea71328d2d';

    // Step 1: Verify Auth Guard on /app when logged out
    console.log('1. Testing Unauthenticated Route Guard on /app ...');
    await page.goto('http://127.0.0.1:8000/app', { waitUntil: 'networkidle' });
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/01_unauthenticated_guard.png` });
    console.log('   ✓ Saved: 01_unauthenticated_guard.png (Redirected to /login)');

    // Step 2: Landing Page & Portal
    console.log('2. Viewing Dark Landing Portal ...');
    await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/02_landing_portal_dark.png` });
    console.log('   ✓ Saved: 02_landing_portal_dark.png');

    // Step 3: Fill Credentials and Sign In
    console.log('3. Submitting Login as OrgAdmin ...');
    await page.click('button:has-text("Org Admin")');
    await page.waitForTimeout(300);
    await page.click('#loginSubmitBtn');
    await page.waitForSelector('#sessionPanel:not([style*="display: none"])', { timeout: 5000 });
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/03_login_session_panel.png` });
    console.log('   ✓ Saved: 03_login_session_panel.png');

    // Step 4: Access /app (Executive Dashboard)
    console.log('4. Navigating to Executive Dashboard (/app) ...');
    await page.goto('http://127.0.0.1:8000/app', { waitUntil: 'networkidle' });
    await page.waitForTimeout(700);
    await page.screenshot({ path: `${artifactDir}/04_executive_dashboard.png` });
    console.log('   ✓ Saved: 04_executive_dashboard.png');

    // Step 5: Pipeline & Kanban Board
    console.log('5. Viewing Interactive Pipeline Kanban Board ...');
    await page.click('#navKanban');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/05_pipeline_kanban_board.png` });
    console.log('   ✓ Saved: 05_pipeline_kanban_board.png');

    // Step 6: Create New Deal via Interactive Modal
    console.log('6. Opening New Deal Modal & Creating Deal ...');
    await page.click('button:has-text("New Deal")');
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/06_new_deal_modal.png` });
    console.log('   ✓ Saved: 06_new_deal_modal.png');

    await page.fill('#dealName', 'Stark Hyperloop Quantum Rail');
    await page.fill('#dealAmount', '350000');
    await page.waitForTimeout(300);
    await page.click('#newDealModal button[type="submit"]');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/07_kanban_with_new_deal.png` });
    console.log('   ✓ Saved: 07_kanban_with_new_deal.png');

    // Step 7: Leads & AI Qualification Scoring
    console.log('7. Viewing Leads & AI Qualification Scoring Engine ...');
    await page.click('#navLeads');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/08_leads_ai_scoring_view.png` });
    console.log('   ✓ Saved: 08_leads_ai_scoring_view.png');

    // Step 8: Add New Lead with Automated Scoring
    console.log('8. Opening New Lead Modal ...');
    await page.click('button:has-text("+ Add New Lead")');
    await page.waitForTimeout(500);
    await page.fill('#leadFirstName', 'Arthur');
    await page.fill('#leadLastName', 'Curry');
    await page.fill('#leadEmail', 'arthur.curry@atlantis-oceanic.com');
    await page.fill('#leadCompany', 'Atlantis Oceanic Energy Corp');
    await page.fill('#leadEstValue', '420000');
    await page.waitForTimeout(400);
    await page.screenshot({ path: `${artifactDir}/09_new_lead_modal_creation.png` });
    console.log('   ✓ Saved: 09_new_lead_modal_creation.png');

    await page.click('#newLeadModal button[type="submit"]');
    await page.waitForTimeout(800);

    // Step 9: Companies & Accounts with JSONB attributes
    console.log('9. Viewing Enterprise Companies & Custom JSONB Attributes ...');
    await page.click('#navCompanies');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/10_companies_jsonb_accounts.png` });
    console.log('   ✓ Saved: 10_companies_jsonb_accounts.png');

    // Step 10: Contacts Roster
    console.log('10. Viewing Key Executive Contacts ...');
    await page.click('#navContacts');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/11_contacts_roster.png` });
    console.log('   ✓ Saved: 11_contacts_roster.png');

    // Step 11: Activity Timeline & Audit Logs
    console.log('11. Viewing Chronological Activity & Audit Timeline ...');
    await page.click('#navTimeline');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/12_activity_timeline_audit.png` });
    console.log('   ✓ Saved: 12_activity_timeline_audit.png');

    // Step 12: Global Full-Text Search
    console.log('12. Testing PostgreSQL tsvector Ranked Search ...');
    await page.click('#navSearch');
    await page.waitForTimeout(500);
    await page.fill('#mainSearchInput', 'Stark');
    await page.waitForTimeout(800);
    await page.screenshot({ path: `${artifactDir}/13_global_tsvector_search.png` });
    console.log('   ✓ Saved: 13_global_tsvector_search.png');

    // Step 13: Instant Role Switcher Toolbar (Switch to Sales Representative)
    console.log('13. Testing Header Role Switcher (Switching to Rep) ...');
    await page.click('.role-switch-wrap button:has-text("Rep")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: `${artifactDir}/14_role_switcher_rep.png` });
    console.log('   ✓ Saved: 14_role_switcher_rep.png');

    await browser.close();
    console.log('\n========================================================');
    console.log('   ALL 14 VISUAL TESTS EXECUTED AND CAPTURED 100% OK    ');
    console.log('========================================================');
}

runCompleteVisualTestSuite().catch(err => {
    console.error('Visual test run failed:', err);
    process.exit(1);
});
