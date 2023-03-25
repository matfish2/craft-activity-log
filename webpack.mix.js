// webpack.mix.js

let mix = require('laravel-mix');
mix.setPublicPath('src/assets/compiled');
mix.js('src/assets/js/main.js', 'activity-log.js').vue();
mix.js('src/assets/js/stats/main.js', 'activity-log-stats.js').vue();

mix.minify('src/assets/compiled/activity-log.js')
mix.minify('src/assets/compiled/activity-log.css')
mix.minify('src/assets/compiled/activity-log-stats.js')
