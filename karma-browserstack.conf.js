// karma.conf.js for BrowserStack

module.exports = function (config) {
    config.set({
        // global config of BrowserStack
        browserStack: {
            build: 'Nothing Private Unit Tests',
            project: 'Nothing Private',
        },
        // base path that will be used to resolve all patterns (eg. files, exclude)
        basePath: '',


        // frameworks to use
        // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
        frameworks: ['jasmine'],

        // list of files / patterns to load in the browser
        files: [
            'node_modules/clientjs/dist/client.min.js',
            'tests/main.spec.browserstack.js',
            'main.js',
        ],
        hostname: 'bs-local.com',

        // test results reporter to use
        // possible values: 'dots', 'progress'
        // available reporters: https://npmjs.org/browse/keyword/karma-reporter
        reporters: ['progress'],


        // web server port
        port: 9876,


        // enable / disable colors in the output (reporters and logs)
        colors: true,


        // level of logging
        // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
        logLevel: config.LOG_INFO,


        // enable / disable watching file and executing tests whenever any file changes
        autoWatch: true,


        // define browsers
        customLaunchers: {
            mac_catalina_safari_latest: {
                base: 'BrowserStack',
                browser: 'Safari',
                browser_version: 'latest',
                os: 'OS X',
                os_version: 'Catalina'
            },
            mac_catalina_opera_latest: {
                base: 'BrowserStack',
                browser: 'Opera',
                browser_version: 'latest',
                os: 'OS X',
                os_version: 'Catalina'
            },
            android_13_Pixel7: {
                base: 'BrowserStack',
                device: 'Google Pixel 7',
                os: 'android',
                os_version: '13.0',
                browser: 'android',
                real_mobile: true
            },
            android_12_Galaxy_S22: {
                base: 'BrowserStack',
                device: 'Samsung Galaxy S22',
                os: 'android',
                os_version: '12.0',
                browser: 'android',
                real_mobile: true
            },
            iOS_16_iPhone14: {
                base: 'BrowserStack',
                os_version : '16',
                device : 'iPhone 14',
                real_mobile : true,
                os: 'ios'
            }
        },

        browsers: [
            'iOS_16_iPhone14',
            'mac_catalina_safari_latest',
            'mac_catalina_opera_latest',
            'android_13_Pixel7',
            'android_12_Galaxy_S22'
        ],
        concurrency: 5,

        // If browser does not capture in given timeout [ms], kill it
        captureTimeout: (1000) * (5*60), // Long Timeout 5 minutes for iphone
        retryLimit: 2,
        // Continuous Integration mode
        // if true, Karma captures browsers, runs the tests and exits
        singleRun: true,
    });
};
