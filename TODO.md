# TODO

- [x] Fix Colleges page bulk toggle / delete confirmation not working
      (caused by duplicate Alpine.js instances — removed manual Alpine import in `app.js`).
- [x] Re-test bulk add — confirmed working.
- [x] Prevent duplicate codes from crashing bulk add (in-batch check added).
- [x] Stop add/edit errors leaking between modals and records.
- [x] Enter key now confirms delete.
- [x] Bulk add error messages no longer show raw field index (e.g. "codes.0").

## Later
- [ ] Remove duplicate `add_condition_and_specs_to_devices_table` migration (harmless, just redundant).