# Laravel Frontend preset for Inertia.js

A Laravel front-end scaffolding preset for [Inertia.js](https://github.com/inertiajs/inertia) and [vuetifyjs](https://vuetifyjs.com).

This preset scaffolding removes the manual steps required to get up and running with Inertia.js.

## Usage

1. Fresh install Laravel >= 7.0 and cd to your app.
2. Install this preset by adding this to your composer.json file then `composer update`.
```json
"repositories": [
    {
        "url": "https://github.com/FahmiChaar/liv-ui-preset.git",
        "type": "git"
    }
],
"require-dev": {
    ...,
    "liv/liv": "*"
}
``` 
4. Laravel will automatically discover this package. No need to register the service provider.

### Installation

1. Use `php artisan liv:ui` to install the scaffolding
2. `npm install && npm run dev`
3. `php artisan serve` (or equivalent) to run server and test preset.

## Generate scaffold
- this command use infyom:scaffold command under the hood with options --fromTable --tableName=(default model name plural lowercase)


`php artisan liv:scaffold User --tableName=users`

## Publish scaffold
- Publish infyom scaffold (stubs)

`php artisan liv:publish`

## More

Learn more about Inertia.js from [@reinink](https://twitter.com/reinink)'s [introductory blog post](https://reinink.ca/articles/introducing-inertia-js).
