var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    minifyCSS = require('gulp-minify-css'),
    autoprefixer = require('gulp-autoprefixer'),
    merge = require('merge-stream'),
    assetsVersionReplace = require('gulp-assets-version-replace');

var plumber = require('gulp-plumber');
var gutil = require('gulp-util');

var gulp_src = gulp.src;
gulp.src = function () {
    return gulp_src.apply(gulp, arguments)
        .pipe(plumber(function (error) {
                // Output an error message
                gutil.log(gutil.colors.red('Error (' + error.plugin + '): ' + error.message));
                // emit the end event, to properly end the task
                this.emit('end');
            })
        );
};


// minify all js and combine
gulp.task('scripts', function () {

    return gulp.src([
        // './resources/assets/js/app.js',
        './bower_components/bootstrap/dist/js/bootstrap.min.js',
        './resources/assets/js/adminlte.min.js',
        './resources/assets/js/autoNumeric.min.js',
        './resources/assets/js/custom.js',
        './resources/assets/js/tests-manger.js',
        './resources/assets/js/mails-manger.js',
        './resources/assets/js/datatables.min.js',
        './resources/assets/js/sweetalert2.min.js',
        './resources/assets/js/jquery.validate.min.js',
        './resources/assets/js/datatable_crud.js',
        './resources/assets/js/client_custom.js',
        './resources/assets/js/assessor_custom.js',
        './resources/assets/js/css_recruiters_custom.js',
        './resources/assets/js/tds_custom.js',
        './resources/assets/js/select2.min.js',
        './resources/assets/js/moment.js',
        './resources/assets/js/datetimepicker.js',
        './node_modules/masonry-layout/dist/masonry.pkgd.js',
        // './resources/assets/js/dropzone.js',
        './resources/assets/js/bootstrap-switch.min.js',
        // './resources/assets/js/bootstrap3-wysihtml5.all.min.js',
        './resources/assets/js/bootstrap-switch.min.js',
        // './resources/assets/js/jquery-ui.min.js',
        './resources/assets/js/jquery-ui-position.min.js',
        './resources/assets/js/jquery.ui.timepicker.js',
        './node_modules/dateformat/lib/dateformat.js',
        './resources/assets/js/jquery.countdown.min.js',
        './resources/assets/js/bootstrap-tagsinput.js',
        './node_modules/plyr/dist/plyr.js',
        './resources/assets/js/Sortable.min.js',
        // './resources/assets/js/chart.min.js',
        './resources/assets/js/bootstrap-slider.min.js',
        './resources/assets/js/dataTables.responsive.min.js',
        './resources/assets/js/bluebird.min.js',
        './resources/assets/js/core-swal-ie11.js',
        './resources/assets/js/daterangepicker.js',
        './resources/assets/js/home-page.js',
        './resources/assets/js/projects-page.js',
        './resources/assets/js/billing-page.js',
        './resources/assets/js/languages-page.js',
        './resources/assets/js/invoices-page.js',
        './resources/assets/js/prices-page.js'

    ])
        .pipe(concat('merged.js'))
        .pipe(gulp.dest('./resources/assets/'))
        .pipe(rename('scripts.min.js'))
        .pipe(uglify())
        //        .pipe(assetsVersionReplace({
        //            replaceTemplateList: [
        //                'resources/views/layouts/app.blade.php'
        //            ]
        //        }))
        .pipe(gulp.dest('./public/js/'));

});

// minify all js and combine
gulp.task('scripts-task-page', function () {

    return gulp.src([
        './resources/assets/js/task-page.js',
        './resources/assets/js/project-page.js'
    ])
        .pipe(concat('merged-task-project.js'))
        .pipe(gulp.dest('./resources/assets/'))
        .pipe(rename('scripts-task-page.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js/'));

});

gulp.task('less', function () {
    return gulp.src('less/*.less')
        .pipe(less().on('error', gutil.log))
        .pipe(gulp.dest('app/css'));
});

// minify all css and combine
gulp.task('styles', function () {


    var sassStream,
        cssStream;

    //compile sass
    sassStream = gulp.src([
        './resources/assets/sass/app.scss',
        './resources/assets/sass/AdminLTE_custom.scss',
        './resources/assets/sass/sidebar.scss',
        './resources/assets/sass/login_custom.scss',
        './resources/assets/sass/datatables_custom.scss',
        './resources/assets/sass/split.scss',
    ])
        .pipe(sass().on('error', sass.logError))
        .pipe(rename('scss.css'))
        .pipe(autoprefixer('>0%'))
        .pipe(gulp.dest('./resources/assets/css'));

    //select additional css files
    cssStream = gulp.src([
        './resources/assets/css/bootstrap.min.css',
        './resources/assets/css/datatables.min.css',
        './resources/assets/css/AdminLTE.min.css',
        './resources/assets/css/_all-skins.min.css',
        './resources/assets/css/sweetalert2.min.css',
        './resources/assets/css/animate.css',
        './resources/assets/css/select2.min.css',
        './resources/assets/css/datetimepicker.min.css',
        './resources/assets/sass/AdminLTE_custom.scss',
        './resources/assets/css/dropzone.css',
        './resources/assets/css/bootstrap-switch.min.css',
        './resources/assets/css/bootstrap3-wysihtml5.min.css',
        './node_modules/plyr/dist/plyr.css',
        './resources/assets/css/bootstrap-slider.min.css',
        './resources/assets/css/responsive.dataTables.min.css',
        './resources/assets/css/daterangepicker.css'
    ]);

    //merge the two streams and concatenate their contents into a single file
    return merge(sassStream, cssStream)
        .pipe(concat('styles.min.css'))
        //.pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9'))
        .pipe(minifyCSS({ keepSpecialComments: 1, processImport: false }))
        //        .pipe(assetsVersionReplace({
        //            replaceTemplateList: [
        //                'resources/views/layouts/app.blade.php'
        //            ]
        //        }))
        .pipe(gulp.dest('./public/css/'));


});

// minify all css and combine
gulp.task('styles-pdf', function () {
    var sassStream,
        cssStream;

    //compile sass
    sassStream = gulp.src(['./resources/assets/sass/app.scss'
        ])
        .pipe(sass().on('error', sass.logError))
        .pipe(rename('scss.css'))
        .pipe(autoprefixer('>0%'))
        .pipe(gulp.dest('./resources/assets/css'));

    //select additional css files
    cssStream = gulp.src([
        './resources/assets/css/bootstrap.min.css'
    ]);

    //merge the two streams and concatenate their contents into a single file
    return merge(sassStream, cssStream)
        .pipe(concat('styles-pdf.min.css'))
        .pipe(minifyCSS())
        .pipe(gulp.dest('./public/css/'));


});

gulp.task('watch', function () {
    gulp.watch('./resources/assets/js/*.js', ['scripts', 'scripts-task-page']);
    gulp.watch('./resources/assets/css/*.css', ['styles']);
    gulp.watch('./resources/assets/sass/**/*.scss', ['styles']);
});

gulp.task('watch-styles', function () {
    gulp.watch('./resources/assets/sass/**/*.scss', ['styles']);
});

gulp.task('watch-billing', function () {
    gulp.watch('./resources/assets/js/*.js', ['scripts', 'scripts-task-page']);
    gulp.watch('./resources/assets/css/*.css', ['styles']);
    gulp.watch('./resources/assets/sass/**/*.scss', ['styles']);
});


gulp.task('default', ['scripts', 'scripts-task-page', 'styles']);
