# MaintenanceBundle
Provides helper to set web app in maintenance mode. This is useful when using automated deploying processes, ensuring website is properly inaccessible during the deploy process.

This bundle provides a flat html fil for maintenance splash screen. This is done __to avoid generating cache during the maintenance time__.

## Installation
`composer require lch/maintenance-bundle`

## Usage

Use the command to toggle maintenance mode :
- `php bin/console lch:maintenance:mode 1` for enabling maintenance (creates a `.maintenance` file in `public/`)
- `php bin/console lch:maintenance:mode 0` for disabling maintenance (remove the `.maintenance` file in `public/`)

When maintenance mode is on, static splash html page will be served with a 503 HTTP code.

## TODO
- make html file configurable
- add options
- Enhance exceptions
- ...
