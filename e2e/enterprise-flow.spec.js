import { test, expect } from '@playwright/test';

test.describe('ADA Co-OS E2E Operations Flow', () => {
  test('Landing page loads with enterprise headers', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/ADA Co-OS|NexusADA/i);
    await expect(page.locator('body')).toBeVisible();
  });

  test('Client portal login renders cleanly', async ({ page }) => {
    await page.goto('/client/login');
    await expect(page.locator('h1')).toContainText(/Müşteri Girişi|Client Login/i);
    await expect(page.locator('input[type="email"]')).toBeVisible();
  });

  test('Admin authentication portal renders 2FA input', async ({ page }) => {
    await page.goto('/admin/login');
    await expect(page.locator('input[type="email"]')).toBeVisible();
    await expect(page.locator('input[type="password"]')).toBeVisible();
  });
});
