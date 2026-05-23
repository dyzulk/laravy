# Laravel Deployment Guide for Wasmer

This document serves as a historical record and a definitive configuration guide for successfully deploying a Laravel application (utilizing Vite, Inertia, and React) to the Wasmer platform.

## 1. Package Manager Resolution (pnpm vs npm)
The project initially utilized `pnpm` (v11). However, deployments on Wasmer failed with the following error:
```
[ERR_PNPM_IGNORED_BUILDS] Ignored build scripts: unrs-resolver@1.12.2
```

**Root Cause:**
Recent versions of `pnpm` (v9 and above) enforce strict security policies that automatically block third-party `postinstall` or build scripts by default. Because the Wasmer build process executes in a headless CI/CD environment, there is no interactive prompt available to approve the execution of these scripts.

**Resolution:**
Migrated the package manager from `pnpm` to standard `npm`.
1. Removed `pnpm-lock.yaml` and `pnpm-workspace.yaml`.
2. Updated the `"packageManager"` field in `package.json` to `"npm"`.
3. Executed `npm install` to generate a standard `package-lock.json`.

---

## 2. Vite Build Resolution (Missing DevDependencies)
Following the migration to `npm`, the Wasmer deployment encountered a new error during the `npm run build` execution phase:
```
Error [ERR_MODULE_NOT_FOUND]: Cannot find package '@laravel/vite-plugin-wayfinder'
```

**Root Cause:**
The Wasmer build environment automatically enforces `NODE_ENV=production` during the NPM installation phase. When operating in production mode, the `npm ci` command strictly ignores all packages declared within the `devDependencies` block. Consequently, mandatory compilation tools such as Vite, TailwindCSS, and the Wayfinder plugin were omitted from the node_modules directory, causing the build to fail.

**Resolution:**
Relocated all frontend build and compilation dependencies from the `devDependencies` block into the primary `dependencies` block within `package.json`. This configuration forces `npm` to install the required build dependencies regardless of the environment mode, ensuring asset compilation succeeds.

---

## 3. Wasmer Dashboard Configuration

To guarantee automated and successful deployments via GitHub triggers, apply the following configuration within the Wasmer App Settings:

*   **Install Command:** `OFF`
    *Rationale: Leaving this disabled allows Wasmer to natively detect the package manager and execute `npm ci` automatically based on the presence of `package-lock.json`.*
*   **Build Command:** `ON`
    Define the following command sequence:
    ```bash
    composer run-script deploy && npm run build
    ```
    *Rationale: This ensures Laravel's optimization scripts execute prior to the Vite frontend asset compilation.*

---

## Conclusion
When deploying to modern serverless platforms or PaaS environments such as Wasmer, it is highly recommended to default to standard `npm` to avoid aggressive security restrictions enforced by alternative package managers. Furthermore, promoting build dependencies from `devDependencies` to `dependencies` is an industry-standard practice to circumvent CI/CD pipeline failures caused by production-mode environment variables during the build phase.
