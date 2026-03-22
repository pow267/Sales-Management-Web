const base = require('@playwright/test');

exports.test = base.test.extend({
  page: async ({ page }, use) => {

    const errors = {
      console: new Set(),
      api: new Set(),
    };

    // ===== CONFIG =====
    const ignoreConsoleErrors = [
      'favicon',
      'ResizeObserver loop limit exceeded',
      'Script error',
      'Failed to load resource',
    ];

    const ignoredResourceTypes = ['image', 'media', 'font'];

    const ignoreUrls = [
      'favicon',
      '/login',
      '/auth',
    ];

    const criticalEndpoints = [
      '/products',
      '/cart',
    ];

    // ===== CONSOLE =====
    const consoleHandler = (msg) => {
      if (
        msg.type() === 'error' &&
        !ignoreConsoleErrors.some(e => msg.text().includes(e))
      ) {
        errors.console.add(msg.text());
      }
    };

    // ===== NETWORK =====
    const responseHandler = (response) => {
      const url = response.url();
      const status = response.status();
      const type = response.request().resourceType();

      if (
        status >= 400 &&
        ![401, 403].includes(status) &&
        !ignoredResourceTypes.includes(type) &&
        !ignoreUrls.some(u => url.includes(u))
      ) {
        errors.api.add(`API ${status}: ${url}`);
      }
    };

    // ===== HOOK =====
    page.on('console', consoleHandler);
    page.on('response', responseHandler);

    // ===== TEST RUN =====
    await use(page);

    // ===== CLEANUP =====
    page.off('console', consoleHandler);
    page.off('response', responseHandler);

    // ===== REPORT =====
    if (errors.console.size > 0 || errors.api.size > 0) {
      console.log('\n===== ERRORS DETECTED =====');

      if (errors.console.size > 0) {
        console.log('\n[Console Errors]');
        errors.console.forEach(e => console.log(e));
      }

      if (errors.api.size > 0) {
        console.log('\n[API Errors]');
        errors.api.forEach(e => console.log(e));
      }

      console.log('===========================\n');
    }

    // ===== ASSERT =====
    const criticalApiErrors = [...errors.api].filter(e =>
      criticalEndpoints.some(ep => e.includes(ep))
    );

    if (criticalApiErrors.length > 0) {
      base.expect(criticalApiErrors.length).toBe(0);
    }

    base.expect(errors.console.size).toBe(0);
  }
});

exports.expect = base.expect;