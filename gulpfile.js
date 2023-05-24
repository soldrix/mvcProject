// Requis
var gulp = require('gulp'),
    less = require('gulp-less'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    cleanCSS = require('gulp-clean-css'),
    clean = require('gulp-clean'),
    csso = require('gulp-csso'),
    autoprefixer = require('gulp-autoprefixer'),
    es = require('event-stream');

var paths = {
    styles: {
        src:{
            less:[
                'ressources/css/**/*.less'
            ],
            css:[
                'node_modules/bootstrap/dist/css/bootstrap.css',
                'node_modules/@fortawesome/fontawesome-pro/css/fontawesome.css'
            ]
        },
        dest: 'public/css/'
    },
    scripts: {
        src: [
            'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
            'node_modules/@fortawesome/fontawesome-pro/js/all.js',
            'node_modules/jquery/dist/jquery.js',
            'ressources/js/**/*.js'

        ],
        dest: 'public/js/'
    }
};

/* Not all tasks need to use streams, a gulpfile is just another node program
 * and you can use all packages available on npm, but it must return either a
 * Promise, a Stream or take a callback and call it
 */
function clear() {


    return gulp.src(['public/js','public/css'], {
        read: false,
        allowEmpty : true
    })
    .pipe(clean({allowEmpty : true}));
}


/*
 * Define our tasks using plain functions
 */
async function styles(){
      return es.merge(
        gulp.src(paths.styles.src.less)
            .pipe(less())
            .pipe(cleanCSS())
            .pipe(autoprefixer())
            // pass in options to the stream
            .pipe(csso())
            .pipe(rename({
                suffix: '.min'
            }))
            .pipe(gulp.dest(paths.styles.dest)),
        gulp.src(paths.styles.src.css)
            .pipe(cleanCSS())
            .pipe(autoprefixer())
            // pass in options to the stream
            .pipe(csso())
            .pipe(rename({
                suffix: '.min'
            }))
            .pipe(gulp.dest(paths.styles.dest))
    );
}

function scripts() {
    return gulp.src(paths.scripts.src)
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(paths.scripts.dest));
}


function watch() {
    gulp.watch(paths.scripts.src,scripts);
    gulp.watch(paths.styles.src.less,styles);
}

/*
 * Specify if tasks run in series or parallel using `gulp.series` and `gulp.parallel`
 */
var build = gulp.series(clear, gulp.parallel(styles, scripts));

/*
 * You can use CommonJS `exports` module notation to declare tasks
 */
exports.clean = clear;
exports.styles = styles;
exports.scripts = scripts;
exports.watch = watch;
exports.build = build;
/*
 * Define default task that can be called by just running `gulp` from cli
 */
exports.default = build;